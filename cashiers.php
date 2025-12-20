<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-light">
        <h5 class="mb-0">Cashiers</h5>
        <button class="btn btn-success btn-sm" id="create_new">
            Add New
        </button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:5%">#</th>
                        <th style="width:30%">Name</th>
                        <th class="text-center" style="width:25%">Log Status</th>
                        <th class="text-center" style="width:25%">Status</th>
                        <th class="text-center" style="width:15%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sql = "SELECT * FROM cashier_list ORDER BY name ASC";
                    $qry = $conn->query($sql);
                    $i = 1;
                    $hasData = false;

                    while ($row = $qry->fetchArray(SQLITE3_ASSOC)):
                        $hasData = true;
                    ?>
                    <tr>
                        <td class="text-center"><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>

                        <!-- Log Status -->
                        <td class="text-center">
                            <?php if ($row['log_status'] == 1): ?>
                                <span class="badge bg-success">In Use</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Not In Use</span>
                            <?php endif; ?>
                        </td>

                        <!-- Account Status -->
                        <td class="text-center">
                            <?php if ($row['status'] == 1): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>

                        <!-- Actions -->
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary edit_data"
                                    data-id="<?= $row['cashier_id'] ?>"
                                    title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <button class="btn btn-outline-danger delete_data"
                                    data-id="<?= $row['cashier_id'] ?>"
                                    data-name="<?= htmlspecialchars($row['name']) ?>"
                                    title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                    <?php if (!$hasData): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No cashiers found.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(function(){

        $('#create_new').click(function(){
            uni_modal('Add New Cashier', 'manage_cashier.php');
        });

        $('.edit_data').click(function(){
            uni_modal(
                'Edit Cashier',
                'manage_cashier.php?id=' + $(this).data('id')
            );
        });

        $('.delete_data').click(function(){
            const id = $(this).data('id');
            const name = $(this).data('name');

            Swal.fire({
                title: 'Delete User?',
                html: `Are you sure you want to delete <b>${name}</b>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    delete_data(id);
                }
            });
        });

    });

    function delete_data(id){
        $('#confirm_modal button').attr('disabled', true);

        $.ajax({
            url: './Actions.php?a=delete_cashier',
            method: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(resp){
                if(resp.status === 'success'){
                    location.reload();
                } else if(resp.status === 'failed' && resp.msg){
                    const el = $('<div class="alert alert-danger">').text(resp.msg);
                    $('#confirm_modal .modal-body').prepend(el);
                } else {
                    alert('An error occurred.');
                }
                $('#confirm_modal button').attr('disabled', false);
            },
            error: function(){
                alert('An error occurred.');
                $('#confirm_modal button').attr('disabled', false);
            }
        });
    }
</script>