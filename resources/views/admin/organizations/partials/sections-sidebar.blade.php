<aside class="colleges-section-list">
    <div class="colleges-section-list-header">Sections</div>
    <nav>
        @foreach ($sections as $slug => $name)
            <div class="d-flex align-items-center position-relative group">
                <a href="{{ route('admin.organizations.show-section', ['college' => $collegeSlug, 'organization' => $organization, 'section' => $slug]) }}"
                   class="colleges-section-item flex-grow-1 {{ $currentSection === $slug ? 'active' : '' }}">
                    {{ $name }}
                </a>
                @if (!in_array($slug, ['overview', 'activities', 'officers', 'gallery']))
                    <form action="{{ route('admin.organizations.delete-section', ['organization' => $organization, 'section' => $slug]) }}" 
                          method="POST" 
                          class="position-absolute end-0 me-2"
                          onsubmit="return confirm('Are you sure you want to delete this custom section?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger p-0 border-0 group-hover-show" title="Delete Section">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
        
        <button type="button" class="colleges-section-item w-100 text-start border-0 bg-transparent text-primary py-3" data-bs-toggle="modal" data-bs-target="#addSectionModal">
            <i class="bi bi-plus-lg me-2"></i> Add Section
        </button>
    </nav>
</aside>
