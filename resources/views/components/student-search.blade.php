@props([
    'placeholder' => 'Search for students...',
    'multiple' => false,
    'selectedStudents' => [],
    'selectedParents' => [],
    'name' => 'selected_students',
    'parentName' => 'selected_parents',
    'recipientType' => 'students'
])

<div class="form-group">
    <label>{{ __('msg.search_students') }}</label>
    <div class="input-group">
        <input type="text"
               class="form-control"
               id="studentSearch"
               placeholder="{{ $placeholder }}"
               autocomplete="off">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <div id="searchResults" class="mt-2" style="max-height: 200px; overflow-y: auto; display: none;">
        <!-- Search results will be populated here -->
    </div>
</div>

@if($multiple)
    <div id="selectedRecipients" class="mt-2">
        @if(count($selectedStudents) > 0 || count($selectedParents) > 0)
            <div class="border p-2 bg-light">
                <strong>{{ __('msg.selected_recipients') }}:</strong>
                <div id="selectedList" class="mt-2">
                    <!-- Selected recipients will be shown here -->
                </div>
            </div>
        @endif
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('studentSearch');
    const searchResults = document.getElementById('searchResults');
    const clearButton = document.getElementById('clearSearch');
    const selectedList = document.getElementById('selectedList');
    let searchTimeout;

    // Search functionality
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });

    // Clear search
    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        searchResults.style.display = 'none';
        searchInput.focus();
    });

    function performSearch(query) {
        fetch('{{ route("communication.search_students") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: 'query=' + encodeURIComponent(query)
        })
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data);
        })
        .catch(error => {
            console.error('Search error:', error);
        });
    }

    function displaySearchResults(data) {
        if (data.students.length === 0 && data.parents.length === 0) {
            searchResults.innerHTML = '<div class="text-muted p-2">{{ __("msg.no_results_found") }}</div>';
            searchResults.style.display = 'block';
            return;
        }

        let html = '';

        // Students results
        if (data.students.length > 0) {
            html += `
                <div class="mb-3">
                    <h6 class="text-primary mb-2">{{ __('msg.students') }}</h6>
                    ${data.students.map(student => `
                        <div class="search-result-item p-2 border-bottom" data-type="student" data-id="${student.id}" style="cursor: pointer;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${student.name}</strong> - ${student.adm_no}
                                    <br>
                                    <small class="text-muted">${student.class_name} - ${student.section_name}</small>
                                    ${student.phone ? `<br><small class="text-muted"><i class="fa fa-phone"></i> ${student.phone}</small>` : ''}
                                    ${student.email ? `<br><small class="text-muted"><i class="fa fa-envelope"></i> ${student.email}</small>` : ''}
                                </div>
                                <button type="button" class="btn btn-sm btn-primary select-student-btn" data-type="student" data-id="${student.id}">
                                    {{ __('msg.select') }}
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        // Parents results
        if (data.parents.length > 0) {
            html += `
                <div class="mb-3">
                    <h6 class="text-success mb-2">{{ __('msg.parents') }}</h6>
                    ${data.parents.map(parent => `
                        <div class="search-result-item p-2 border-bottom" data-type="parent" data-id="${parent.student_id}" style="cursor: pointer;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${parent.name}</strong> (Parent of ${parent.student_name})
                                    <br>
                                    <small class="text-muted">${parent.class_name} - ${parent.section_name}</small>
                                    ${parent.phone ? `<br><small class="text-muted"><i class="fa fa-phone"></i> ${parent.phone}</small>` : ''}
                                    ${parent.email ? `<br><small class="text-muted"><i class="fa fa-envelope"></i> ${parent.email}</small>` : ''}
                                </div>
                                <button type="button" class="btn btn-sm btn-success select-student-btn" data-type="parent" data-id="${parent.student_id}">
                                    {{ __('msg.select') }}
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        searchResults.innerHTML = html;
        searchResults.style.display = 'block';

        // Add click handlers
        document.querySelectorAll('.select-student-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const type = this.getAttribute('data-type');
                const id = this.getAttribute('data-id');
                selectRecipient(type, id);
            });
        });

        // Add hover effects
        document.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
            });
            item.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });
    }

    function selectRecipient(type, id) {
        @if($multiple)
            addToSelectedRecipients(type, id);
        @else
            // For single selection, you can implement custom logic here
            console.log('Selected:', type, id);
        @endif

        // Check the corresponding checkbox in the recipient list
        const checkbox = document.querySelector(`input[type="checkbox"][value="${id}"]`);
        if (checkbox) {
            checkbox.checked = true;
            // Trigger change event to update any dependent logic
            checkbox.dispatchEvent(new Event('change'));
        }

        // Hide search results
        searchResults.style.display = 'none';
        searchInput.value = '';
    }

    @if($multiple)
    function addToSelectedRecipients(type, id) {
        // Check if already selected
        const existingInput = document.querySelector(`input[name="${type === 'student' ? '{{ $name }}' : '{{ $parentName }}'}[]"][value="${id}"]`);
        if (existingInput) {
            return; // Already selected
        }

        // Get recipient data (you might need to fetch this or pass it differently)
        const recipientData = getRecipientData(type, id);
        if (!recipientData) return;

        // Create hidden input
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `${type === 'student' ? '{{ $name }}' : '{{ $parentName }}'}[]`;
        input.value = id;

        // Add to form
        const form = document.querySelector('form');
        if (form) {
            form.appendChild(input);
        }

        // Add to visual list
        updateSelectedList();
    }

    function getRecipientData(type, id) {
        // This is a simplified version - in practice, you might want to store the data
        // or make another API call to get the recipient details
        return { type: type, id: id, name: 'Recipient Name' };
    }

    function updateSelectedList() {
        const selectedStudents = Array.from(document.querySelectorAll('input[name="{{ $name }}[]"]')).map(input => input.value);
        const selectedParents = Array.from(document.querySelectorAll('input[name="{{ $parentName }}[]"]')).map(input => input.value);

        if (selectedStudents.length === 0 && selectedParents.length === 0) {
            if (selectedList) selectedList.innerHTML = '';
            return;
        }

        let html = '';

        if (selectedStudents.length > 0) {
            html += '<div class="mb-2"><strong>{{ __("msg.students") }}:</strong> ' + selectedStudents.length + ' selected</div>';
        }

        if (selectedParents.length > 0) {
            html += '<div class="mb-2"><strong>{{ __("msg.parents") }}:</strong> ' + selectedParents.length + ' selected</div>';
        }

        if (selectedList) selectedList.innerHTML = html;
    }

    // Initialize selected list
    updateSelectedList();
    @endif
});
</script>

<style>
.search-result-item:hover {
    background-color: #f8f9fa !important;
}

.select-student-btn {
    min-width: 60px;
}
</style>