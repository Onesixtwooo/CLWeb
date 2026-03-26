{{-- GRADUATE OUTCOMES (List) --}}

<div class="col-12 mb-4">
    <label for="graduate_outcomes_title" class="form-label fw-bold">Section Title</label>
    <input type="text" name="graduate_outcomes_title" id="graduate_outcomes_title" class="form-control @error('graduate_outcomes_title') is-invalid @enderror" value="{{ old('graduate_outcomes_title', $department->graduate_outcomes_title ?? '') }}" placeholder="e.g., Graduate Outcomes">
    @error('graduate_outcomes_title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-12 mb-4">
    <h5 class="fw-bold mb-3">Existing Outcomes</h5>
    @if(isset($department->outcomes) && $department->outcomes->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 50px">Sort</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th style="width: 100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($department->outcomes as $outcome)
                        <tr>
                            <td class="align-middle text-center">{{ $outcome->sort_order }}</td>
                            <td class="align-middle fw-bold">{{ $outcome->title }}</td>
                            <td class="align-middle">{{ Str::limit($outcome->description, 150) }}</td>
                            <td class="align-middle">
                                @if($outcome->image)
                                    <img src="{{ asset($outcome->image) }}" alt="Outcome Image" style="height: 40px; width: auto;">
                                @else
                                    <span class="text-muted small">No Image</span>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                <button type="submit" name="delete_outcome" value="{{ $outcome->id }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this outcome?')">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info">No graduate outcomes added yet.</div>
    @endif
</div>

<div class="col-12">
    <hr class="my-4">
    <h5 class="fw-bold mb-3">Add New Outcome</h5>
</div>

<div class="col-12">
    <label for="new_outcome_title" class="form-label">Title</label>
    <input type="text" name="new_outcome_title" id="new_outcome_title" class="form-control" placeholder="e.g., Technical Proficiency">
</div>

<div class="col-12">
    <label for="new_outcome_description" class="form-label">Description</label>
    <textarea name="new_outcome_description" id="new_outcome_description" class="form-control quill-editor" rows="4" placeholder="Description of the outcome..."></textarea>
</div>

<div class="col-12">
    <label for="new_outcome_image" class="form-label">Image (Optional)</label>
    <input type="file" name="new_outcome_image" id="new_outcome_image" class="form-control" accept="image/*">
</div>

<div class="col-12">
    <label for="new_outcome_sort" class="form-label">Sort Order</label>
    <input type="number" name="new_outcome_sort" id="new_outcome_sort" class="form-control" value="0">
</div>
