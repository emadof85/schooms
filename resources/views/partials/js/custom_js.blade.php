<script>

    function getClassSections(class_id, target_selector = null){
        var url = '{{ route('get_class_sections', [':id']) }}';
        url = url.replace(':id', class_id);
        var section = target_selector ? $(target_selector) : ($('#selectedSection').length ? $('#selectedSection') : $('#section_id'));

        $.ajax({
            dataType: 'json',
            url: url,
            success: function (resp) {
                //console.log(resp);
                section.empty();
                section.append($('<option>', {
                    value: '',
                    text: 'Select Section'
                }));
                $.each(resp, function (i, data) {
                    section.append($('<option>', {
                        value: data.id,
                        text: data.name
                    }));
                });

                // Trigger student filtering after sections are loaded
                if (typeof filterStudentsBySection === 'function') {
                    filterStudentsBySection();
                }
            }
        })
    }

    function filterStudentsBySection(){
        var grade = $('#selectedGrade').val();
        var class_id = $('#selectedClass').val();
        var section = $('#selectedSection').val();
        var recipientType = $('#recipientType').val() || 'students';

        // Make AJAX call to filter students
        $.ajax({
            url: '{{ route('filter_students') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                grade: grade,
                class_id: class_id,
                section: section,
                recipient_type: recipientType
            },
            success: function(resp) {
                // Update the recipient list
                $('.recipient-checkboxes').html(resp.html);
            }
        });
    }


    function getClassSubjects(class_id){
        var url = '{{ route('get_class_subjects', [':id']) }}';
        url = url.replace(':id', class_id);
        var section = $('#section_id');
        var subject = $('#subject_id');

        $.ajax({
            dataType: 'json',
            url: url,
            success: function (resp) {
                console.log(resp);
                section.empty();
                subject.empty();
                $.each(resp.sections, function (i, data) {
                    section.append($('<option>', {
                        value: data.id,
                        text: data.name
                    }));
                });
                $.each(resp.subjects, function (i, data) {
                    subject.append($('<option>', {
                        value: data.id,
                        text: data.name
                    }));
                });

            }
        })
    }

    function getEducationalStageClasses(educational_stage_id){
        var url = '{{ route('get_educational_stage_classes', [':id']) }}';
        url = url.replace(':id', educational_stage_id);
        var classes = $('#selectedClass');
        var sections = $('#selectedSection');

        $.ajax({
            dataType: 'json',
            url: url,
            success: function (resp) {
                console.log(resp);
                classes.empty();
                sections.empty();
                classes.append($('<option>', {
                    value: '',
                    text: 'All Classes'
                }));
                sections.append($('<option>', {
                    value: '',
                    text: 'All Sections'
                }));
                $.each(resp, function (i, data) {
                    classes.append($('<option>', {
                        value: data.id,
                        text: data.name
                    }));
                });
            }
        })
    }


    {{--Notifications--}}

    @if (session('pop_error'))
    pop({msg : '{{ session('pop_error') }}', type : 'error'});
    @endif

    @if (session('pop_warning'))
    pop({msg : '{{ session('pop_warning') }}', type : 'warning'});
    @endif

 @if (session('pop_success'))
    pop({msg : '{{ session('pop_success') }}', type : 'success', title: 'GREAT!!'});
    @endif

    @if (session('flash_info'))
      flash({msg : '{{ session('flash_info') }}', type : 'info'});
    @endif

    @if (session('flash_success'))
      flash({msg : '{{ session('flash_success') }}', type : 'success'});
    @endif

    @if (session('flash_warning'))
      flash({msg : '{{ session('flash_warning') }}', type : 'warning'});
    @endif

     @if (session('flash_error') || session('flash_danger'))
      flash({msg : '{{ session('flash_error') ?: session('flash_danger') }}', type : 'danger'});
    @endif

    {{--End Notifications--}}

    function pop(data){
        swal({
            title: data.title ? data.title : 'Oops...',
            text: data.msg,
            icon: data.type
        });
    }

    function flash(data){
        new PNotify({
            text: data.msg,
            type: data.type,
            hide : data.type !== "danger"
        });
    }

    function confirmDelete(id) {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this item!",
            icon: "warning",
            buttons: true,
            dangerMode: true
        }).then(function(willDelete){
            if (willDelete) {
             $('form#item-delete-'+id).submit();
            }
        });
    }

    function confirmReset(id) {
        swal({
            title: "Are you sure?",
            text: "This will reset this item to default state",
            icon: "warning",
            buttons: true,
            dangerMode: true
        }).then(function(willDelete){
            if (willDelete) {
             $('form#item-reset-'+id).submit();
            }
        });
    }

    $('form#ajax-reg').on('submit', function(ev){
        ev.preventDefault();
        submitForm($(this), 'store');
        $('#ajax-reg-t-0').get(0).click();
    });

    $('form.ajax-pay').on('submit', function(ev){
        ev.preventDefault();
        submitForm($(this), 'store');

//        Retrieve IDS
        var form_id = $(this).attr('id');
        var td_amt = $('td#amt-'+form_id);
        var td_amt_paid = $('td#amt_paid-'+form_id);
        var td_bal = $('td#bal-'+form_id);
        var input = $('#val-'+form_id);

        // Get Values
        var amt = parseInt(td_amt.data('amount'));
        var amt_paid = parseInt(td_amt_paid.data('amount'));
        var amt_input = parseInt(input.val());

//        Update Values
        amt_paid = amt_paid + amt_input;
        var bal = amt - amt_paid;

        td_bal.text(''+bal);
        td_amt_paid.text(''+amt_paid).data('amount', ''+amt_paid);
        input.attr('max', bal);
        bal < 1 ? $('#'+form_id).fadeOut('slow').remove() : '';
    });

    $('form.ajax-store').on('submit', function(ev){
        ev.preventDefault();
        submitForm($(this), 'store');
        var div = $(this).data('reload');
        div ? reloadDiv(div) : '';
    });

    $('form.ajax-update').on('submit', function(ev){
        ev.preventDefault();
        submitForm($(this));
        var div = $(this).data('reload');
        div ? reloadDiv(div) : '';
    });

    $('.download-receipt').on('click', function(ev){
        ev.preventDefault();
        $.get($(this).attr('href'));
        flash({msg : '{{ 'Download in Progress' }}', type : 'info'});
    });

    function reloadDiv(div){
        var url = window.location.href;
        url = url + ' '+ div;
        $(div).load( url );
    }

    function submitForm(form, formType){
        var btn = form.find('button[type=submit]');
        disableBtn(btn);
        var ajaxOptions = {
            url:form.attr('action'),
            type:'POST',
            cache:false,
            processData:false,
            dataType:'json',
            contentType:false,
            data:new FormData(form[0])
        };
        var req = $.ajax(ajaxOptions);
        req.done(function(resp){
            resp.ok && resp.msg
               ? flash({msg:resp.msg, type:'success'})
               : flash({msg:resp.msg, type:'danger'});
            hideAjaxAlert();
            enableBtn(btn);
            formType == 'store' ? clearForm(form) : '';
            scrollTo('body');
            return resp;
        });
        req.fail(function(e){
            if (e.status == 422){
                var errors = e.responseJSON.errors;
                displayAjaxErr(errors);
            }
           if(e.status == 500){
               displayAjaxErr([e.status + ' ' + e.statusText + ' Please Check for Duplicate entry or Contact School Administrator/IT Personnel'])
           }
            if(e.status == 404){
               displayAjaxErr([e.status + ' ' + e.statusText + ' - Requested Resource or Record Not Found'])
           }
            enableBtn(btn);
            return e.status;
        });
    }

    function disableBtn(btn){
        var btnText = btn.data('text') ? btn.data('text') : 'Submitting';
        btn.prop('disabled', true).html('<i class="icon-spinner mr-2 spinner"></i>' + btnText);
    }

    function enableBtn(btn){
        var btnText = btn.data('text') ? btn.data('text') : 'Submit Form';
        btn.prop('disabled', false).html(btnText + '<i class="icon-paperplane ml-2"></i>');
    }

    function displayAjaxErr(errors){
        $('#ajax-alert').show().html(' <div class="alert alert-danger border-0 alert-dismissible" id="ajax-msg"><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
        $.each(errors, function(k, v){
            $('#ajax-msg').append('<span><i class="icon-arrow-right5"></i>' + v + '</span><br/>');
        });
        scrollTo('body');
    }

    function scrollTo(el){
        $('html, body').animate({
            scrollTop:$(el).offset().top
        }, 2000);
    }

    function hideAjaxAlert(){
        $('#ajax-alert').hide();
    }

    function clearForm(form){
        form.find('.select, .select-search').val([]).select2({ placeholder: 'Select...'});
        form[0].reset();
    }

    // Fix dropdown positioning in datatables
    var activeDatatableDropdown = null;

    $(document).on('shown.bs.dropdown', '.datatable-button-html5-columns .dropdown', function (e) {
        var dropdown = $(this);
        var menu = dropdown.find('.dropdown-menu');
        var toggle = dropdown.find('[data-toggle="dropdown"]');

        if (toggle.length && toggle.offset()) {
            // Calculate position relative to viewport
            var toggleOffset = toggle.offset();
            var toggleHeight = toggle.outerHeight();

            // Move menu to body and position fixed
            menu.appendTo('body').css({
                position: 'fixed',
                top: toggleOffset.top + toggleHeight,
                left: toggleOffset.left,
                zIndex: 1050,
                minWidth: '160px',
                display: 'block'
            });

            // Store reference for hiding
            menu.data('original-parent', dropdown);
            activeDatatableDropdown = dropdown;
        }
    });

    // Reset position when hidden
    $(document).on('hidden.bs.dropdown', '.datatable-button-html5-columns .dropdown', function () {
        var menu = $(this).find('.dropdown-menu');
        var originalParent = menu.data('original-parent');

        if (originalParent && originalParent.length) {
            menu.appendTo(originalParent).css({
                position: '',
                top: '',
                left: '',
                zIndex: '',
                display: ''
            });
        }
        activeDatatableDropdown = null;
    });

    // Handle click outside to hide
    $(document).on('click', function (e) {
        var fixedMenus = $('body > .dropdown-menu[style*="position: absolute"]');
        if (fixedMenus.length && !$(e.target).closest('.dropdown-menu, [data-toggle="dropdown"]').length) {
            fixedMenus.each(function() {
                var menu = $(this);

                // Find the original dropdown
                var originalParent = menu.data('original-parent');
                if (originalParent && originalParent.length) {
                    var dropdown = originalParent;

                    // Directly hide the dropdown
                    dropdown.removeClass('show');
                    menu.removeClass('show');

                    // Move menu back
                    menu.appendTo(originalParent).css({
                        position: '',
                        top: '',
                        left: '',
                        zIndex: '',
                        display: ''
                    });
                }
            });
            activeDatatableDropdown = null;
        }
    });

</script>