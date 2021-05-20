<header id="header" class="sticky-top">
  <div class="row bg-topbar pt-2 pb-1">
    <div class="col-2">
      <!-- <div id="nav-drawer" class="pl-2">
        <input id="nav-input" type="checkbox" class="nav-unshown">
        <label id="nav-open" for="nav-input"><span></span></label>
        <label class="nav-unshown" id="nav-close" for="nav-input"></label>
        <div id="nav-content">
          <ul>
            <li>
              <a href="#">メニューリンク1</a>
            </li>
            <li>
              <a href="#">メニューリンク2</a>
            </li>
            <li>
              <a href="#">メニューリンク3</a>
            </li>
          </ul>
        </div>
      </div> -->
      @yield('logo')
    </div>
    <div class="col-7" style="text-align:center; align-self:center;">
      @yield('title')
      <!-- <a class="text-white page-title" href="/">@yield('title')</a> -->
    </div>
    <div class="col-3" style="padding-left:0px;text-align:right; align-self:center; padding-right: 2px;">
      @yield('logout')
      <!-- <a class="text-white pr-2" href="{{ route('user.logout') }}">ログアウト</a> -->
    </div>
  </div>
</header>