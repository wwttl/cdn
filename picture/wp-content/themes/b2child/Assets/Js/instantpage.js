/*! instant.page v5.2.0 - (C) 2019-2023 Alexandre Dieulot - https://instant.page/license */

let _chromiumMajorVersionInUserAgent = null
  , _allowQueryString
  , _allowExternalLinks
  , _useWhitelist
  , _delayOnHover = 65
  , _lastTouchTimestamp
  , _mouseoverTimer
  , _preloadedList = new Set()

const DELAY_TO_NOT_BE_CONSIDERED_A_TOUCH_INITIATED_ACTION = 1111

init()

function init() {
  const isSupported = document.createElement('link').relList.supports('prefetch')
  // instant.page is meant to be loaded with <script type=module>
  // (though sometimes webmasters load it as a regular script).
  // So it鈥檚 normally executed (and must not cause JavaScript errors) in:
  // - Chromium 61+
  // - Gecko in Firefox 60+
  // - WebKit in Safari 10.1+ (iOS 10.3+, macOS 10.10+)
  //
  // The check above used to check for IntersectionObserverEntry.isIntersecting
  // but module scripts support implies this compatibility 鈥� except in Safari
  // 10.1鈥�12.0, but this prefetch check takes care of it.

  if (!isSupported) {
    return
  }

  const handleVaryAcceptHeader = 'instantVaryAccept' in document.body.dataset || 'Shopify' in window
  // The `Vary: Accept` header when received in Chromium 79鈥�109 makes prefetches
  // unusable, as Chromium used to send a different `Accept` header.
  // It鈥檚 applied on all Shopify sites by default, as Shopify is very popular
  // and is the main source of this problem.
  // `window.Shopify` only exists on 鈥渃lassic鈥� Shopify sites. Those using
  // Hydrogen (Remix SPA) aren鈥檛 concerned.

  const chromiumUserAgentIndex = navigator.userAgent.indexOf('Chrome/')
  if (chromiumUserAgentIndex > -1) {
    _chromiumMajorVersionInUserAgent = parseInt(navigator.userAgent.substring(chromiumUserAgentIndex + 'Chrome/'.length))
  }
  // The user agent client hints API is a theoretically more reliable way to
  // get Chromium鈥檚 version鈥� but it鈥檚 not available in Samsung Internet 20.
  // It also requires a secure context, which would make debugging harder,
  // and is only available in recent Chromium versions.
  // In practice, Chromium browsers never shy from announcing "Chrome" in
  // their regular user agent string, as that maximizes their compatibility.

  if (handleVaryAcceptHeader && _chromiumMajorVersionInUserAgent && _chromiumMajorVersionInUserAgent < 110) {
    return
  }

  const mousedownShortcut = 'instantMousedownShortcut' in document.body.dataset
  _allowQueryString = 'instantAllowQueryString' in document.body.dataset
  _allowExternalLinks = 'instantAllowExternalLinks' in document.body.dataset
  _useWhitelist = 'instantWhitelist' in document.body.dataset

  const eventListenersOptions = {
    capture: true,
    passive: true,
  }

  let useMousedown = false
  let useMousedownOnly = false
  let useViewport = false
  if ('instantIntensity' in document.body.dataset) {
    const intensity = document.body.dataset.instantIntensity

    if (intensity.startsWith('mousedown')) {
      useMousedown = true
      if (intensity == 'mousedown-only') {
        useMousedownOnly = true
      }
    }
    else if (intensity.startsWith('viewport')) {
      const isNavigatorConnectionSaveDataEnabled = navigator.connection && navigator.connection.saveData
      const isNavigatorConnectionLike2g = navigator.connection && navigator.connection.effectiveType && navigator.connection.effectiveType.includes('2g')
      if (!isNavigatorConnectionSaveDataEnabled && !isNavigatorConnectionLike2g) {
        if (intensity == "viewport") {
          if (document.documentElement.clientWidth * document.documentElement.clientHeight < 450000) {
            useViewport = true
            // Smartphones are the most likely to have a slow connection, and
            // their small screen size limits the number of links (and thus
            // server load).
            //
            // Foldable phones (being expensive as of 2023), tablets and PCs
            // generally have a decent connection, and a big screen displaying
            // more links that would put more load on the server.
            //
            // iPhone 14 Pro Max (want): 430脳932 = 400鈥�760
            // Samsung Galaxy S22 Ultra with display size set to 80% (want):
            // 450脳965 = 434鈥�250
            // Small tablet (don鈥檛 want): 600脳960 = 576鈥�000
            // Those number are virtual screen size, the viewport (used for
            // the check above) will be smaller with the browser鈥檚 interface.
          }
        }
        else if (intensity == "viewport-all") {
          useViewport = true
        }
      }
    }
    else {
      const milliseconds = parseInt(intensity)
      if (!isNaN(milliseconds)) {
        _delayOnHover = milliseconds
      }
    }
  }

  if (!useMousedownOnly) {
    document.addEventListener('touchstart', touchstartListener, eventListenersOptions)
  }

  if (!useMousedown) {
    document.addEventListener('mouseover', mouseoverListener, eventListenersOptions)
  }
  else if (!mousedownShortcut) {
      document.addEventListener('mousedown', mousedownListener, eventListenersOptions)
  }

  if (mousedownShortcut) {
    document.addEventListener('mousedown', mousedownShortcutListener, eventListenersOptions)
  }

  if (useViewport) {
    let requestIdleCallbackOrFallback = window.requestIdleCallback
    // Safari has no support as of 16.3: https://webkit.org/b/164193
    if (!requestIdleCallbackOrFallback) {
      requestIdleCallbackOrFallback = (callback) => {
        callback()
        // A smarter fallback like setTimeout is not used because devices that
        // may eventually be eligible to a Safari version supporting prefetch
        // will be very powerful.
        // The weakest devices that could be eligible are the 2017 iPad and
        // the 2016 MacBook.
      }
    }

    requestIdleCallbackOrFallback(function observeIntersection() {
      const intersectionObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const anchorElement = entry.target
            intersectionObserver.unobserve(anchorElement)
            preload(anchorElement.href)
          }
        })
      })

      document.querySelectorAll('a').forEach((anchorElement) => {
        if (isPreloadable(anchorElement)) {
          intersectionObserver.observe(anchorElement)
        }
      })
    }, {
      timeout: 1500,
    })
  }
}

function touchstartListener(event) {
  _lastTouchTimestamp = performance.now()
  // Chrome on Android triggers mouseover before touchcancel, so
  // `_lastTouchTimestamp` must be assigned on touchstart to be measured
  // on mouseover.

  const anchorElement = event.target.closest('a')

  if (!isPreloadable(anchorElement)) {
    return
  }

  preload(anchorElement.href, 'high')
}

function mouseoverListener(event) {
  if (performance.now() - _lastTouchTimestamp < DELAY_TO_NOT_BE_CONSIDERED_A_TOUCH_INITIATED_ACTION) {
    return
  }

  if (!('closest' in event.target)) {
    return
    // Without this check sometimes an error 鈥渆vent.target.closest is not a function鈥� is thrown, for unknown reasons
    // That error denotes that `event.target` isn鈥檛 undefined. My best guess is that it鈥檚 the Document.
    //
    // Details could be gleaned from throwing such an error:
    //throw new TypeError(`instant.page non-element event target: timeStamp=${~~event.timeStamp}, type=${event.type}, typeof=${typeof event.target}, nodeType=${event.target.nodeType}, nodeName=${event.target.nodeName}, viewport=${innerWidth}x${innerHeight}, coords=${event.clientX}x${event.clientY}, scroll=${scrollX}x${scrollY}`)
  }
  const anchorElement = event.target.closest('a')

  if (!isPreloadable(anchorElement)) {
    return
  }

  anchorElement.addEventListener('mouseout', mouseoutListener, {passive: true})

  _mouseoverTimer = setTimeout(() => {
    preload(anchorElement.href, 'high')
    _mouseoverTimer = undefined
  }, _delayOnHover)
}

function mousedownListener(event) {
  const anchorElement = event.target.closest('a')

  if (!isPreloadable(anchorElement)) {
    return
  }

  preload(anchorElement.href, 'high')
}

function mouseoutListener(event) {
  if (event.relatedTarget && event.target.closest('a') == event.relatedTarget.closest('a')) {
    return
  }

  if (_mouseoverTimer) {
    clearTimeout(_mouseoverTimer)
    _mouseoverTimer = undefined
  }
}

function mousedownShortcutListener(event) {
  if (performance.now() - _lastTouchTimestamp < DELAY_TO_NOT_BE_CONSIDERED_A_TOUCH_INITIATED_ACTION) {
    return
  }

  const anchorElement = event.target.closest('a')

  if (event.which > 1 || event.metaKey || event.ctrlKey) {
    return
  }

  if (!anchorElement) {
    return
  }

  anchorElement.addEventListener('click', function (event) {
    if (event.detail == 1337) {
      return
    }

    event.preventDefault()
  }, {capture: true, passive: false, once: true})

  const customEvent = new MouseEvent('click', {view: window, bubbles: true, cancelable: false, detail: 1337})
  anchorElement.dispatchEvent(customEvent)
}

function isPreloadable(anchorElement) {
  if (!anchorElement || !anchorElement.href) {
    return
  }

  if (_useWhitelist && !('instant' in anchorElement.dataset)) {
    return
  }

  if (anchorElement.origin != location.origin) {
    let allowed = _allowExternalLinks || 'instant' in anchorElement.dataset
    if (!allowed || !_chromiumMajorVersionInUserAgent) {
      // Chromium-only: see comment on 鈥渞estrictive prefetch鈥�
      返回
    }
  }

  如果 （！[' http:',' https:']。包含（锚元素。草案)) {
    返回
  }

  如果 （锚元素。草案==' http:'&&位置。草案==' https:') {
    返回
  }

  如果 （！_ allowQueryString & & anchor element。搜索&& !（“即时” 在锚元素。资料组)) {
    返回
  }

  如果 （锚元素。混杂&& anchorElement。路径名+锚点元素。搜索==位置。路径名+位置。搜索) {
    返回
  }

  如果 （'无实例' 在锚元素。资料组) {
    返回
  }

  返回 真实的
}

功能 事先装好（全球资源定位器(Uniform Resource Locator),提取优先级='自动') {
  如果 （_preloadedList。有（全球资源定位器(Uniform Resource Locator))) {
    返回
  }

  常数 linkElement=文档。createElement（'链接')
linkElement。能量损耗率='预取'
linkElement。href= url

linkElement。提取优先级=提取优先级
  //默认情况下，预取以低优先级加载。
  //当有鈥檚这个预取很有可能会用在
  //近期(=在触摸/鼠标事件之后)，给它一个高优先级会有帮助
  //使页面加载更快，以防有其他资源加载。
  //隐式优先化它意味着取消其他所有资源的优先级
  //那个鈥檚正在页面上加载。由于HTML文档通常很大
  //比其他资源(特别是图像和JavaScript)小，并且
  //一旦初始页面被充分加载，就发生预取，
  //这种带宽窃取应该很少是有害的。

linkElement。如同='文档'
  // as=document仅包含铬，并允许跨来源预取
  //可用于导航。他们称之为鈥渞限制性预取鈥并打算
  //删除它:https://crbug.com/1352371
  //
  //这份来自Chrome团队的文档日期为2022年8月10日
  //https://docs . Google . com/document/d/1x 232 kjuiwif-k 08 vpnfv 85 svcrhkaxldfuia 5 koqi 6m
  //声称(我还没有鈥檛测试)数据和电池节电模式以及
  //禁用预加载的设置不会禁用限制性预取，
  //与常规预取不同。那鈥檚适合在触摸/鼠标上预取
  //事件，但预取视区中的每个链接时可能会出错。

文档。头。appendChild（linkElement)

_preloadedList。增加（全球资源定位器(Uniform Resource Locator))
}
