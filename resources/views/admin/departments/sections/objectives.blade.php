<input type="hidden" name="_objectives_edit" value="1">
{{-- OBJECTIVES (List) --}}
<div class="col-12 mb-4">
    <h5 class="fw-bold mb-3">Existing Objectives</h5>
    @if(isset($department->objectives) && $department->objectives->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 80px">Sort</th>
                        <th>Content</th>
                        <th style="width: 100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($department->objectives as $objective)
                        <tr>
                            <td class="align-middle text-center">
                                <input type="number" name="objectives[{{ $objective->id }}][sort_order]" class="form-control form-control-sm text-center" value="{{ $objective->sort_order }}">
                            </td>
                            <td class="align-middle">
                                <textarea name="objectives[{{ $objective->id }}][content]" class="form-control form-control-sm quill-editor" rows="2">{{ $objective->content }}</textarea>
                            </td>
                            <td class="align-middle text-center">
                                <button type="submit" name="delete_objective" value="{{ $objective->id }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this objective?')">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info">No objectives added yet.</div>
    @endif
</div>

<div class="col-12">
    <hr class="my-4">
    <h5 class="fw-bold mb-3">Add New Objective</h5>
</div>

<div class="col-12">
    <label for="new_objective_content" class="form-label">Objective Content</label>
    <textarea name="new_objective_content" id="new_objective_content" class="form-control quill-editor" rows="3" placeholder="Enter objective content..."></textarea>
</div>

<div class="col-12">
    <label for="new_objective_sort" class="form-label">Sort Order</label>
    <input type="number" name="new_objective_sort" id="new_objective_sort" class="form-control" value="0">
</div>
