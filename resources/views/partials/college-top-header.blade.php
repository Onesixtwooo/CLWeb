        <!-- Top header bar (main contact bar above main header) -->
        <div class="engineering-top-header">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 engineering-top-header-inner">
                    <div class="d-flex flex-wrap align-items-center gap-3 gap-md-4">
                        <a href="{{ url('/') }}" class="engineering-top-header-clsu d-flex align-items-center flex-shrink-0" aria-label="Back to CLSU main">
                            @php
                                $globalLogoPath = \App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp');
                                $globalLogoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($globalLogoPath);
                            @endphp
                            <img src="{{ $globalLogoUrl }}" alt="Central Luzon State University" class="engineering-top-header-clsu-img">
                        </a>
                        <a href="https://www.google.com/maps?q=Central+Luzon+State+University+Mu%C3%B1oz+Nueva+Ecija" target="_blank" rel="noopener noreferrer" class="engineering-top-header-link d-flex align-items-center gap-1" aria-label="Location">
                            <span class="engineering-top-header-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            </span>
                            <span class="d-none d-md-inline">Science City of Muñoz</span>
                        </a>
                        <a href="mailto:{{ \App\Models\Setting::get('admin_president_email', 'op@clsu.edu.ph') }}" class="engineering-top-header-link d-flex align-items-center gap-1">
                            <span class="engineering-top-header-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </span>
                            {{ \App\Models\Setting::get('admin_president_email', 'op@clsu.edu.ph') }}
                        </a>
                        <a href="tel:{{ \App\Models\Setting::get('admin_president_phone', '(044) 940 8785') }}" class="engineering-top-header-link d-flex align-items-center gap-1">
                            <span class="engineering-top-header-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            </span>
                            {{ \App\Models\Setting::get('admin_president_phone', '(044) 940 8785') }}
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($collegeContact?->facebook)
                        <a href="{{ $collegeContact->facebook }}" class="engineering-top-header-social" title="Facebook" aria-label="Facebook" target="_blank">f</a>
                        @endif
                        @if($collegeContact?->instagram)
                        <a href="{{ $collegeContact->instagram }}" class="engineering-top-header-social" title="Instagram" aria-label="Instagram" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        </a>
                        @endif
                        @if(!empty($collegeContact?->custom_links))
                            @foreach($collegeContact->custom_links as $link)
                                @if(!empty($link))
                                <a href="{{ $link }}" class="engineering-top-header-social" title="External Link" aria-label="External Link" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                </a>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
