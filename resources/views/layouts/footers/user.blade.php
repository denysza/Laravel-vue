<footer id="footer" class="fixed-bottom">
  <ul class="nav nav-fill bg-topbar">
    <li class="nav-item">
      <a class="footer-nav-link text-white" href="{{ route('user.search_painter') }}">
        <div class="link-area">
          <img class="icon-img" src="/image/search-icon.png">
          <span class="icon-label">探 す</span>
        </div>
      </a>
    </li>
    <li class="nav-item">
      <a class="footer-nav-link text-white" href="{{ route('user.construction_case_list') }}">
        <div class="link-area">
          <img class="icon-img" src="/image/edit-icon.png">
          <span class="icon-label">施工事例</span>
        </div>
      </a>      
    </li>
    <li class="nav-item">
      <a class="footer-nav-link text-white" href="{{ route('favorite') }}">
        <div class="link-area">
          <img class="icon-img" src="/image/favorite-footer-icon.png">
          <span class="icon-label">お気に入り</span>
        </div>
      </a>
    </li>
    <li class="nav-item">
      <a class="footer-nav-link text-white" href="{{ route('user.search_painter') }}">
        <div class="link-area">
          <img class="icon-img" src="/image/message-icon.png">
          <span class="icon-label">商談</span>
        </div>
      </a>
    </li>
    <li class="nav-item">
      <a class="footer-nav-link text-white" href="{{ route('user.login') }}">
        <div class="link-area">
          <img class="icon-img" src="/image/login-icon.png">
          <span class="icon-label">ログイン</span>
        </div>
      </a>
    </li>
  </ul>
</footer>