<?php 
require_once('./DBConnection.php');

if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM queue_list WHERE queue_id = '{$_GET['id']}'");
    $res = $qry->fetchArray(SQLITE3_ASSOC);
}
?>

<style>
    #uni_modal .modal-footer {
        display: none;
    }

    .queue-card {
        border-left: 6px solid #0dcaf0;
    }

    .queue-number {
        font-size: 4rem;
        font-weight: 700;
        letter-spacing: 2px;
    }

    @media print {
        button { display: none !important; }
    }
</style>

<div class="container-fluid px-3">

    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success text-center">
            Your queue number has been generated successfully.
        </div>
    <?php endif; ?>

    <!-- QUEUE CARD -->
    <div id="outprint">
        <div class="card queue-card shadow-sm mb-3">
            <div class="card-body text-center py-4">

                <div class="queue-number mb-2">
                    <?= htmlspecialchars($res['queue'] ?? '---') ?>
                </div>

                <div class="fs-5 text-muted">
                    <?= htmlspecialchars($res['customer_name'] ?? '') ?>
                </div>

            </div>
        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="d-flex justify-content-center gap-3">
        <button class="btn btn-success px-4" id="print">
            <i class="fa-solid fa-print me-1"></i> Print
        </button>

        <button class="btn btn-outline-secondary px-4"
                data-bs-dismiss="modal">
            <i class="fa-solid fa-xmark me-1"></i> Close
        </button>
    </div>

</div>

<script>
    $(function(){

        $('#print').on('click', function () {

            const head = $('head').clone();
            const content = $('#outprint').clone();

            head.find('title').text('Queue Number');

            const printWindow = window.open('', '_blank', 'width=700,height=500');

            printWindow.document.write(`
                <html>
                    ${head.prop('outerHTML')}
                    <body class="p-4">
                        ${content.prop('outerHTML')}
                    </body>
                </html>
            `);

            printWindow.document.close();

            setTimeout(() => {
                printWindow.print();
                setTimeout(() => {
                    printWindow.close();
                    $('#uni_modal').modal('hide');
                }, 300);
            }, 500);
        });

    });
</script>
