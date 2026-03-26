<!-- Edit Item Modal -->
<div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-700" id="itemModalLabel">Add Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="itemForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="method-container"></div>
                <div class="modal-body py-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-700 text-uppercase mb-2">Upload Image Here</label>
                            <div id="item-modal-preview" class="item-preview-container mb-2 text-center bg-light rounded d-flex align-items-center justify-content-center border" style="width: 100%; aspect-ratio: 1/1;">
                                <i class="bi bi-image text-muted opacity-50 fs-1"></i>
                            </div>
                            <input type="hidden" name="image" id="item-modal-image">
                            <div class="mt-2">
                                <label for="item-modal-upload" class="btn btn-outline-secondary btn-sm w-100 rounded-pill mb-0">Upload Image</label>
                                <input type="file" name="image_upload" id="item-modal-upload" class="d-none" accept="image/*">
                            </div>
                            <div class="form-text small mt-2">Upload image directly.</div>
                        </div>
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div id="field-name-container" class="col-md-6">
                                    <label id="label-name" class="form-label text-muted small fw-700 text-uppercase mb-2">Name/Title</label>
                                    <input type="text" name="name" id="item-modal-name" class="form-control rounded-3">
                                </div>
                                <div id="field-role-container" class="col-md-6">
                                    <label id="label-role" class="form-label text-muted small fw-700 text-uppercase mb-2">Role/Position</label>
                                    <input type="text" name="role" id="item-modal-role" class="form-control rounded-3">
                                </div>
                                <div id="field-date-container" class="col-md-6" style="display: none;">
                                    <label class="form-label text-muted small fw-700 text-uppercase mb-2">Date</label>
                                    <input type="text" name="date" id="item-modal-date" class="form-control rounded-3" placeholder="e.g. Oct 2024">
                                </div>
                                <div id="field-caption-container" class="col-12" style="display: none;">
                                    <label class="form-label text-muted small fw-700 text-uppercase mb-2">Caption</label>
                                    <input type="text" name="caption" id="item-modal-caption" class="form-control rounded-3">
                                </div>
                                <div id="field-description-container" class="col-12">
                                    <label id="label-description" class="form-label text-muted small fw-700 text-uppercase mb-2">Description</label>
                                    <textarea name="description" id="item-modal-description" rows="5" class="form-control rounded-3 quill-editor"></textarea>
                                </div>
                                <div id="field-visible-container" class="col-12" style="display: none;">
                                    <div class="form-check form-switch mt-2">
                                        <input type="hidden" name="is_visible" value="0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="is_visible" id="item-modal-visible" value="1" checked>
                                        <label class="form-check-label fw-600" for="item-modal-visible">Visible on public page</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="item-modal-submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-700" id="addSectionModalLabel">Add New Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.organizations.add-section', $organization) }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label for="section_title" class="form-label text-muted small fw-700 text-uppercase mb-2">Section Title</label>
                        <input type="text" 
                               class="form-control form-control-lg rounded-3 border-light-subtle" 
                                id="section_title" 
                               name="title" 
                               placeholder="e.g. History, Achievements, Objectives" 
                               required>
                        <div class="form-text small mt-2">
                            This will create a new dynamic section for your organization page.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Create Section</button>
                </div>
            </form>
        </div>
    </div>
</div>
