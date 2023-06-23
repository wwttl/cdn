/*! instant.page v5.2.0 - (C) 2019-2023 Alexandre Dieulot - https://instant.page/license */

让 _chromiumMajorVersionInUserAgent = null
  , _allowQueryString
  , _allowExternalLinks
  , _useWhitelist
  , _delayOnHover = 65
  , _lastTouchTimestamp
  , _mouseover定时器
  , _preloadedList = new Set()

常量 DELAY_TO_NOT_BE_CONSIDERED_A_TOUCH_INITIATED_ACTION = 1111

在里面（）

函数初始化（）{
  const isSupported = document.createElement('link').relList.supports('prefetch')
  // instant.page 应该用 <script type=module> 加载
  //（尽管有时网站管理员将其加载为常规脚本）。
  // 因此它在以下位置正常执行（并且不得导致 JavaScript 错误）：
  // - 铬 61+
  // - Firefox 60+ 中的 Gecko
  // - Safari 10.1+ 中的 WebKit（iOS 10.3+、macOS 10.10+）
  //
  // 上面的检查用于检查 IntersectionObserverEntry.isIntersecting
  // 但模块脚本支持意味着这种兼容性——Safari 除外
  // 10.1-12.0，但是这个预取检查会处理它。

  如果（！isSupported）{
    返回
  }

  const handleVaryAcceptHeader = 'instantVaryAccept' in document.body.dataset ||窗口中的“Shopify”
  // 在 Chromium 79-109 中接收到的 `Vary: Accept` 标头会进行预取
  // 不可用，因为 Chromium 用于发送不同的 `Accept` 标头。
  // 它默认应用于所有 Shopify 网站，因为 Shopify 非常受欢迎
  // 这是这个问题的主要根源。
  // `window.Shopify` 仅存在于“经典”Shopify 网站上。那些使用
  // 氢（Remix SPA）不关心。

  const chromiumUserAgentIndex = navigator.userAgent.indexOf('Chrome/')
  if (chromiumUserAgentIndex > -1) {
    _chromiumMajorVersionInUserAgent = parseInt(navigator.userAgent.substring(chromiumUserAgentIndex + 'Chrome/'.length))
  }
  // 用户代理客户端提示 API 是一种理论上更可靠的方法
  // 获取 Chromium 版本，但在 Samsung Internet 20 中不可用。
  // 它还需要一个安全的上下文，这将使调试变得更加困难，
  // 并且仅在最近的 Chromium 版本中可用。
  // 实际上，Chromium 浏览器从不羞于在
  // 他们的常规用户代理字符串，因为这最大限度地提高了他们的兼容性。

  if (handleVaryAcceptHeader && _chromiumMajorVersionInUserAgent && _chromiumMajorVersionInUserAgent < 110) {
    返回
  }

  document.body.dataset 中的 const mousedownShortcut = 'instantMousedownShortcut'
  document.body.dataset 中的 _allowQueryString = 'instantAllowQueryString'
  document.body.dataset 中的 _allowExternalLinks = 'instantAllowExternalLinks'
  document.body.dataset 中的 _useWhitelist = 'instantWhitelist'

  const eventListenersOptions = {
    捕获：真实，
    被动：真实，
  }

  让 useMousedown = false
  让 useMousedownOnly = false
  让 useViewport = false
  if ('instantIntensity' in document.body.dataset) {
    const 强度 = document.body.dataset.instantIntensity

    if (intensity.startsWith('mousedown')) {
      useMousedown = true
      if (强度 == '仅鼠标按下') {
        仅使用鼠标按下 = true
      }
    }
    否则 if (intensity.startsWith('viewport')) {
      const isNavigatorConnectionSaveDataEnabled = navigator.connection && navigator.connection.saveData
      const isNavigatorConnectionLike2g = navigator.connection && navigator.connection. effectiveType && navigator.connection. effectiveType.includes('2g')
      if (!isNavigatorConnectionSaveDataEnabled && !isNavigatorConnectionLike2g) {
        如果（强度==“视口”）{
          if (document.documentElement.clientWidth * document.documentElement.clientHeight < 450000) {
            使用视口 = true
            // 智能手机的连接速度最有可能较慢，并且
            // 它们的小屏幕尺寸限制了链接的数量（因此
            // 服务器负载）。
            //
            // 可折叠手机（截至 2023 年价格昂贵）、平板电脑和 PC
            // 通常有良好的连接，并且有大屏幕显示
            // 更多链接会给服务器带来更多负载。
            //
            // iPhone 14 Pro Max（想要）：430脑932=400760
            // 三星 Galaxy S22 Ultra，显示尺寸设置为 80%（想要）：
            // 450 大脑 965 = 434 250
            // 小平板电脑（不要）：600脑960=576×000
            // 这些数字是虚拟屏幕尺寸、视口（用于
            // 上面的检查）会随着浏览器界面的变化而变小。
          }
        }
        else if (强度 == "viewport-all") {
          使用视口 = true
        }
      }
    }
    别的 {
      const 毫秒 = parseInt(强度)
      if (!isNaN(毫秒)) {
        _delayOnHover = 毫秒
      }
    }
  }

  如果（！useMousedownOnly）{
    document.addEventListener('touchstart', touchstartListener, eventListenersOptions)
  }

  如果（！useMousedown）{
    document.addEventListener('mouseover', mouseoverListener, eventListenersOptions)
  }
  否则如果（！mousedownShortcut）{
      document.addEventListener('mousedown', mousedownListener, eventListenersOptions)
  }

  如果（鼠标按下快捷键）{
    document.addEventListener('mousedown', mousedownShortcutListener, eventListenersOptions)
  }

  如果（使用视口）{
    让 requestIdleCallbackOrFallback = window.requestIdleCallback
    // Safari 从 16.3 开始不支持：https://webkit.org/b/164193
    if (!requestIdleCallbackOrFallback) {
      requestIdleCallbackOrFallback = (回调) => {
        打回来（）
        // 不使用像 setTimeout 这样的更智能的回退，因为设备
        // 最终可能有资格获得支持预取的 Safari 版本
        // 将非常强大。
        // 可能符合资格的最弱设备是 2017 年的 iPad 和
        // 2016 款 MacBook。
      }
    }

    requestIdleCallbackOrFallback(函数observeIntersection() {
      const intersectionObserver = new IntersectionObserver((条目) => {
        条目.forEach((条目) => {
          if (entry.isIntersecting) {
            constanchorElement=entry.target
            intersectionObserver.unobserve(anchorElement)
            预加载（anchorElement.href）
          }
        })
      })

      document.querySelectorAll('a').forEach((anchorElement) => {
        如果（isPreloadable（anchorElement））{
          交叉观察者.observe(anchorElement)
        }
      })
    }, {
      超时：1500，
    })
  }
}

函数 touchstartListener(事件) {
  _lastTouchTimestamp = 性能.now()
  // Android 上的 Chrome 在 touchcancel 之前触发 mouseover，所以
  // `_lastTouchTimestamp` 必须在要测量的 touchstart 上分配
  // 鼠标悬停时。

  constanchorElement = event.target.closest('a')

  if (!isPreloadable(anchorElement)) {
    返回
  }

  预加载（anchorElement.href，'高'）
}

函数鼠标悬停监听器（事件）{
  if (performance.now() - _lastTouchTimestamp < DELAY_TO_NOT_BE_CONSIDERED_A_TOUCH_INITIATED_ACTION) {
    返回
  }

  if (!(event.target 中'最接近的')) {
    返回
    // 如果没有此检查，有时会出于未知原因抛出错误“event.target.closest is not a function”
    // 该错误表示“event.target”未定义。我最好的猜测是它是文档。
    //
    // 可以通过抛出此类错误来收集详细信息：
    //抛出 new TypeError(`instant.page 非元素事件目标: timeStamp=${~~event.timeStamp}, type=${event.type}, typeof=${typeof event.target}, nodeType=${ event.target.nodeType}，nodeName=${event.target.nodeName}，视口=${innerWidth}x${innerHeight}，coords=${event.clientX}x${event.clientY}，滚动=${滚动X}x${滚动Y}`)
  }
  constanchorElement = event.target.closest('a')

  if (!isPreloadable(anchorElement)) {
    返回
  }

  anchorElement.addEventListener('mouseout', mouseoutListener, {passive: true})

  _mouseoverTimer = setTimeout(() => {
    预加载（anchorElement.href，'高'）
    _mouseoverTimer = 未定义
  }, _delayOnHover)
}

函数 mousedownListener(事件) {
  constanchorElement = event.target.closest('a')

  if (!isPreloadable(anchorElement)) {
    返回
  }

  预加载（anchorElement.href，'高'）
}

函数 mouseoutListener(事件) {
  if (event.relatedTarget && event.target.closest('a') == event.relatedTarget.closest('a')) {
    返回
  }

  如果（_mouseoverTimer）{
    清除超时（_mouseoverTimer）
    _mouseoverTimer = 未定义
  }
}

函数 mousedownShortcutListener(事件) {
  if (performance.now() - _lastTouchTimestamp < DELAY_TO_NOT_BE_CONSIDERED_A_TOUCH_INITIATED_ACTION) {
    返回
  }

  constanchorElement = event.target.closest('a')

  if (event.which > 1 || event.metaKey || event.ctrlKey) {
    返回
  }

  if (!anchorElement) {
    返回
  }

  anchorElement.addEventListener('点击', 函数(事件) {
    if (事件.detail == 1337) {
      返回
    }

    event.preventDefault()
  }, {捕获: true, 被动: false, 一次: true})

  const customEvent = new MouseEvent('点击', {视图: 窗口, 气泡: true, 可取消: false, 详细信息: 1337})
  anchorElement.dispatchEvent(customEvent)
}

函数 isPreloadable(anchorElement) {
  if (!anchorElement || !anchorElement.href) {
    返回
  }

  if (_useWhitelist && !(anchorElement.dataset 中的'instant')) {
    返回
  }

  if (anchorElement.origin != location.origin) {
    让允许= _allowExternalLinks || anchorElement.dataset 中的“即时”
    if (!allowed || !_chromiumMajorVersionInUserAgent) {
      // 仅 Chromium：请参阅“限制性预取”的评论
      返回
    }
  }

  if (!['http:', 'https:'].includes(anchorElement.protocol)) {
    返回
  }

  if (anchorElement.protocol == 'http:' && location.protocol == 'https:') {
    返回
  }

  if (!_allowQueryString &&anchorElement.search && !('instant' inanchorElement.dataset)) {
    返回
  }

  if (anchorElement.hash &&anchorElement.pathname +anchorElement.search == location.pathname + location.search) {
    返回
  }

  if (anchorElement.dataset 中的“noInstant”) {
    返回
  }

  返回真
}

函数预加载(url, fetchPriority = 'auto') {
  如果（_preloadedList.has（url））{
    返回
  }

  const linkElement = document.createElement('link')
  linkElement.rel = '预取'
  linkElement.href = url

  linkElement.fetchPriority = fetchPriority
  // 默认情况下，预取以低优先级加载。
  // 当这个预取很有可能被用在
  // 近期（= 触摸/鼠标事件之后），给予其高优先级会有所帮助
  // 在有其他资源加载的情况下使页面加载速度更快。
  // 隐含地优先考虑它意味着取消所有其他资源的优先级
  // 正在加载到页面上。由于 HTML 文档通常较多
  // 小于其他资源（特别是图像和 JavaScript），并且
  // 一旦初始页面充分加载就会发生预取，
  // 这种带宽盗窃行为很少会造成损害。

  linkElement.as = '文档'
  // as=document 仅限 Chromium，并允许跨域预取
  // 可用于导航。他们称之为“限制性预取”并打算
  // 删除它：https://crbug.com/1352371
  //
  // Chrome 团队于 2022 年 8 月 10 日发布的这份文档
  // https://docs.google.com/document/d/1x232KJUIwIf-k08vpNfV85sVCRHkAxldfuIA5KOqi6M
  // 声称（我没有测试过）数据保护和电池保护模式以及
  // 禁用预加载的设置不会禁用限制性预取，
  // 与常规预取不同。这非常适合在触摸/鼠标上预取
  // 事件，但在预取视口中的每个链接时可能会很糟糕。

  document.head.appendChild(linkElement)

  _preloadedList.add(url)
}