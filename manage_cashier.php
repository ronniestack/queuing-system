<?php
require_once("DBConnection.php");

if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM cashier_list WHERE cashier_id = '{$_GET['id']}'");
    $cashier = $qry->fetchArray(SQLITE3_ASSOC);
}
?>
<div class="container-fluid">

    <!-- Alert -->
    <div id="alert-box" class="alert d-none" role="alert"></div>

    <form id="cashier-form">
        <input type="hidden" name="id" value="<?= $cashier['cashier_id'] ?? '' ?>">

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text"
                   name="name"
                   class="form-control"
                   required
                   autofocus
                   value="<?= htmlspecialchars($cashier['name'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status"
                    class="form-select"
                    required>
                <option value="1" <?= isset($cashier['status']) && $cashier['status'] == 1 ? 'selected' : '' ?>>
                    Active
                </option>
                <option value="0" <?= isset($cashier['status']) && $cashier['status'] == 0 ? 'selected' : '' ?>>
                    Inactive
                </option>
            </select>
        </div>
    </form>

</div>
<script>
    $(function(){

        $('#cashier-form').on('submit', function(e){
            e.preventDefault();

            const form = $(this);
            const alertBox = $('#alert-box');
            const submitBtn = $('#uni_modal button[type="submit"]');

            alertBox.addClass('d-none').removeClass('alert-success alert-danger');
            submitBtn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: './Actions.php?a=save_cashier',
                method: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(resp){
                    if (resp.status === 'success') {
                        alertBox
                            .removeClass('d-none')
                            .addClass('alert-success')
                            .text(resp.msg || 'Cashier saved successfully.');

                        $('#uni_modal').on('hidden.bs.modal', function(){
                            location.reload();
                        });

                        if (!form.find('input[name="id"]').val()) {
                            form[0].reset();
                        }
                    } else {
                        alertBox
                            .removeClass('d-none')
                            .addClass('alert-danger')
                            .text(resp.msg || 'An error occurred.');
                    }

                    submitBtn.prop('disabled', false).text('Save');
                },
                error: function(){
                    alertBox
                        .removeClass('d-none')
                        .addClass('alert-danger')
                        .text('An error occurred.');

                    submitBtn.prop('disabled', false).text('Save');
                }
            });
        });

    });
</script>
