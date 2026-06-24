<div class="lang-component">
    <button type="button" class="lang-btn" aria-haspopup="listbox" aria-expanded="false">
        <span class="lang-flag" aria-hidden="true"><svg width="18" height="12" viewBox="0 0 19 12" xmlns="http://www.w3.org/2000/svg">
                <rect width="19" height="12" fill="#b22234" />
                <g fill="#fff">
                    <rect y="1" width="19" height="1" />
                    <rect y="3" width="19" height="1" />
                    <rect y="5" width="19" height="1" />
                    <rect y="7" width="19" height="1" />
                    <rect y="9" width="19" height="1" />
                </g>
                <rect width="8" height="6" fill="#3c3b6e" />
            </svg></span>
        <span class="lang-label">English</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="margin-left:6px">
            <path d="M6 9l6 6 6-6" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </button>
    <ul class="lang-list" role="listbox">
        <li data-lang="en" role="option">
            <span class="flag-us">
                <!-- US flag (simplified) -->
                <svg width="18" height="12" viewBox="0 0 19 12" xmlns="http://www.w3.org/2000/svg">
                    <rect width="19" height="12" fill="#b22234" />
                    <g fill="#fff">
                        <rect y="1" width="19" height="1" />
                        <rect y="3" width="19" height="1" />
                        <rect y="5" width="19" height="1" />
                        <rect y="7" width="19" height="1" />
                        <rect y="9" width="19" height="1" />
                    </g>
                    <rect width="8" height="6" fill="#3c3b6e" />
                </svg>
            </span>
            <span>English</span>
        </li>
        <li data-lang="ta" role="option">
            <span class="flag-in">
                <!-- India flag (simplified) -->
                <svg width="18" height="12" viewBox="0 0 18 12" xmlns="http://www.w3.org/2000/svg">
                    <rect width="18" height="12" fill="#f93" />
                    <rect y="4" width="18" height="4" fill="#fff" />
                    <rect y="8" width="18" height="4" fill="#128807" />
                    <circle cx="9" cy="6" r="1" fill="#1280c6" />
                </svg>
            </span>
            <span>தமிழ்</span>
        </li>
    </ul>

    <script>
        (function() {
            const script = document.currentScript;
            const root = script && script.parentElement && script.parentElement.classList.contains('lang-component') ?
                script.parentElement :
                null;
            if (!root) return;
            const btn = root.querySelector('.lang-btn');
            const list = root.querySelector('.lang-list');
            const items = list.querySelectorAll('li');
            const label = root.querySelector('.lang-label');
            const flag = root.querySelector('.lang-flag');

            function setLang(l) {
                localStorage.setItem('site_lang', l);
                document.documentElement.lang = l === 'ta' ? 'ta' : 'en';
                // update UI
                if (l === 'ta') {
                    label.innerText = 'தமிழ்';
                    flag.innerHTML = '<svg width="18" height="12" viewBox="0 0 18 12"><rect width="18" height="12" fill="#f93"/><rect y="4" width="18" height="4" fill="#fff"/><rect y="8" width="18" height="4" fill="#128807"/><circle cx="9" cy="6" r="1" fill="#1280c6"/></svg>';
                } else {
                    label.innerText = 'English';
                    flag.innerHTML = '<svg width="18" height="12" viewBox="0 0 19 12" xmlns="http://www.w3.org/2000/svg"><rect width="19" height="12" fill="#b22234"/><g fill="#fff"><rect y="1" width="19" height="1"/><rect y="3" width="19" height="1"/><rect y="5" width="19" height="1"/><rect y="7" width="19" height="1"/><rect y="9" width="19" height="1"/></g><rect width="8" height="6" fill="#3c3b6e"/></svg>';
                }
                // call page-level handler if exists
                if (window.applyLanguage) window.applyLanguage(l);
                // dispatch event
                window.dispatchEvent(new CustomEvent('language.change', {
                    detail: {
                        lang: l
                    }
                }));
            }

            // init
            const saved = localStorage.getItem('site_lang') || 'en';
            setTimeout(() => setLang(saved), 0);

            // toggle list
            btn.addEventListener('click', function(e) {
                const open = list.style.display === 'block';
                list.style.display = open ? 'none' : 'block';
                btn.setAttribute('aria-expanded', open ? 'false' : 'true');
            });

            items.forEach(it => {
                it.addEventListener('click', function() {
                    const l = this.getAttribute('data-lang');
                    setLang(l);
                    list.style.display = 'none';
                    btn.setAttribute('aria-expanded', 'false');
                });
            });

            // close on outside click
            document.addEventListener('click', function(e) {
                if (!root.contains(e.target)) {
                    list.style.display = 'none';
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
        })();
    </script>
</div>