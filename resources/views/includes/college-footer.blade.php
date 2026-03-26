    <!-- Footer -->
    <footer id="contact" class="footer footer-rich" style="background-image: linear-gradient(135deg, rgba(18, 18, 18, 0.9), rgba(18, 18, 18, 0.9)), url('{{ asset('images/CLSU.jpg') }}');">
        <div class="container">
            <div class="footer-grid footer-grid-rich">
                <div class="footer-column footer-brand">
                    <div class="footer-logos">
                        <img src="{{ Vite::asset('resources/images/seal/1.png') }}" alt="Freedom of Information Seal">
                        <img src="{{ Vite::asset('resources/images/seal/2.png') }}" alt="Transparency Seal">
                        <img src="{{ Vite::asset('resources/images/seal/3.png') }}" alt="Philippines Seal" class="seal-transparent">
                        @if(isset($collegeLogoUrl) && $collegeLogoUrl)
                        <img src="{{ $collegeLogoUrl }}" alt="{{ $collegeName ?? 'College' }} Logo">
                        @endif
                    </div>
                    <h3>{{ strtoupper($collegeName ?? 'College of Engineering') }}</h3>
                    <p>Science City of Muñoz, Nueva Ecija, Philippines 3120</p>
                    <div class="footer-divider"></div>
                    <ul class="footer-contact">
                        <li>{{ $collegeName ?? 'College of Engineering' }}, Central Luzon State University, Science City of Muñoz, Nueva Ecija, Philippines</li>
                        @if(isset($institute) && $institute->email)
                            <li><a href="mailto:{{ $institute->email }}">{{ $institute->email }}</a></li>
                        @elseif(isset($department) && $department->email)
                            <li><a href="mailto:{{ $department->email }}">{{ $department->email }}</a></li>
                        @elseif(isset($collegeContact) && $collegeContact->email)
                            <li><a href="mailto:{{ $collegeContact->email }}">{{ $collegeContact->email }}</a></li>
                        @else
                            <li><a href="mailto:{{ $collegeEmail ?? 'cen@clsu.edu.ph' }}">{{ $collegeEmail ?? 'cen@clsu.edu.ph' }}</a></li>
                        @endif

                        @if(isset($institute) && $institute->phone)
                            <li><a href="tel:{{ preg_replace('/[^0-9]/', '', $institute->phone) }}">{{ $institute->phone }}</a></li>
                        @elseif(isset($department) && $department->phone)
                            <li><a href="tel:{{ preg_replace('/[^0-9]/', '', $department->phone) }}">{{ $department->phone }}</a></li>
                        @elseif(isset($collegeContact) && $collegeContact->phone)
                            <li><a href="tel:{{ preg_replace('/[^0-9]/', '', $collegeContact->phone) }}">{{ $collegeContact->phone }}</a></li>
                        @else
                            <li><a href="tel:{{ preg_replace('/[^0-9]/', '', $collegePhone ?? '0449408785') }}">{{ $collegePhone ?? '(044) 940 8785' }}</a></li>
                        @endif
                    </ul>
                    <div class="footer-map">
                        <iframe
                            title="CLSU Map"
                            src="https://www.google.com/maps?q=Central%20Luzon%20State%20University%20Mu%C3%B1oz%20Nueva%20Ecija&output=embed"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <div class="footer-social">
                        @php
                            $facebook = $institute->social_facebook ?? $department->social_facebook ?? $collegeContact?->facebook;
                            $twitter = $institute->social_x ?? $department->social_x ?? $collegeContact?->twitter;
                            $instagram = $institute->social_instagram ?? $department->social_instagram ?? $collegeContact?->instagram;
                            $linkedin = $institute->social_linkedin ?? $department->social_linkedin ?? $collegeContact?->linkedin;
                            $youtube = $institute->social_youtube ?? $department->social_youtube ?? $collegeContact?->youtube;
                            $customLinks = $collegeContact?->custom_links ?? [];
                        @endphp

                        @if($facebook)
                            <a href="{{ $facebook }}" target="_blank" title="Facebook">f</a>
                        @endif
                        @if($twitter)
                            <a href="{{ $twitter }}" target="_blank" title="Twitter">𝕏</a>
                        @endif
                        @if($instagram)
                            <a href="{{ $instagram }}" target="_blank" title="Instagram">📷</a>
                        @endif
                        @if($linkedin)
                            <a href="{{ $linkedin }}" target="_blank" title="LinkedIn">in</a>
                        @endif
                        @if($youtube)
                            <a href="{{ $youtube }}" target="_blank" title="YouTube">▶</a>
                        @endif
                        @if(!empty($customLinks))
                            @foreach($customLinks as $link)
                                @if(!empty($link))
                                <a href="{{ $link }}" target="_blank" title="External Link" aria-label="External Link">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                </a>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    <p class="footer-copy">&copy; Copyright {{ date('Y') }} Central Luzon State University All Rights Reserved</p>
                </div>

                <div class="footer-column">
                    <h3 class="footer-heading">RECENT POSTS</h3>
                    <ul class="footer-list">
                        @if(isset($recentPosts) && $recentPosts->count() > 0)
                            @foreach($recentPosts as $post)
                                <li>
                                    <a href="{{ route('news.announcement.detail', ['college' => $collegeSlug ?? 'engineering', 'slug' => $post->slug]) }}">
                                        {{ Str::limit($post->title, 50) }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li><a href="#">No recent posts</a></li>
                        @endif
                    </ul>

                    <h3 class="footer-heading mt-lg">E-SERVICES</h3>
                    <ul class="footer-list">
                        <li><a href="#">Downloads</a></li>
                        <li><a href="#">Publications</a></li>
                        <li><a href="#">Knowledge Sharing & Learning Resources</a></li>
                        <li><a href="#">AgriATM</a></li>
                        <li><a href="#">Kamalig Booking System</a></li>
                    </ul>
                </div>

                <div class="footer-column footer-feedback">
                    <h3 class="footer-heading">FEEDBACK AND GRIEVANCE DESK</h3>
                    <p>
                        Central Luzon State University values the voices of its students, faculty, staff, and the people it serves
                        and is committed to continuously improve its services. As part of our commitment to quality and excellence,
                        we encourage you to share your feedback, concerns, and suggestions.
                    </p>
                    <p>
                        To ensure your inputs are heard and addressed, we provide the following official channels for receiving
                        feedback and grievances.
                    </p>
                    <ul class="footer-contact">
                        <li><a href="mailto:feedback@clsu.edu.ph">feedback@clsu.edu.ph</a></li>
                        <li><a href="tel:+639537267511">+63 9537 267 511</a></li>
                        <li><a href="tel:+63449407030">(044) 940 7030</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
