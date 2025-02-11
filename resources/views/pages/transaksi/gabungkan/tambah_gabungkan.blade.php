@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4">{{ $title }}</h4>
            <form action="{{ route('tambah-gabungkan')}}" method="POST">
                @csrf
                <div class="form-row align-items-center">
                    <div class="form-group col-md-2 ">
                        <label for="kode_barang">Kode Barang</label>
                        <input type="text" id="kode_barang" class="form-control"  placeholder="Kode Barang" autofocus>
                    </div>
                    <div class="form-group col-md-2 ml-3 ">
                        <label for="merek">Merek</label>
                        <input type="text" class="form-control" id="merek"   placeholder="Merek">
                    </div>
                    <div class="form-group ml-3 ">
                        <label for="jumlah">Jumlah</label>
                        <input type="text" class="form-control" id="jumlah" placeholder="Jumlah">
                    </div>
                    <div class="form-group ml-3 mt-3" >
                        <button class="btn btn-success" type="submit" style="margin-top: 12px">Tambahkan</button>
                    </div>
                </div>

                <button class="btn btn-danger btn-import mb-3 btn-import" id="btn-import">Import</button>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center" >No</th>
                                <th scope="col" style="text-align: center">Kode Barang</th>
                                <th scope="col" style="text-align: center">Merek</th>
                                <th scope="col" style="text-align: center">Jumlah</th>
                                <th scope="col" style="text-align: center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <tr>
                                <td colspan="3" style="text-align: end">Total</td>
                                <td id="total-cell">0</td>
                                <td></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="total_ball" id="total_jumlah" value="0">
                <div class="form-group mt-5">
                    <div class="d-flex justify-content-end">
                        <a href="/gabungkan" class="btn btn-danger mr-3">Batal</a>
                        <button class="btn btn-success" type="submit">Simpan</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="modal fade bd-example-modal-import" style="display: none;" id="importmodal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true"></div>
@endsection

@push('js')
<script>
    function updateFileName() {
        var input = document.getElementById('customFile');
        var label = document.querySelector('.custom-file-label');
        label.textContent = input.files[0] ? input.files[0].name : 'Choose file';
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const kodeBarangInput = document.getElementById('kode_barang');
        const merekInput = document.getElementById('merek');
        const jumlahInput = document.getElementById('jumlah');
        const addButton = document.querySelector('button[type="submit"]');
        const tableBody = document.querySelector('table tbody');

        let rowCount = 0;

        addButton.addEventListener('click', function(e) {
            e.preventDefault();

            const kodeBarang = kodeBarangInput.value;
            const merek = merekInput.value;
            const jumlah = jumlahInput.value;

            const newRow = `
            <tr>
                <td></td>
                <td>${kodeBarang}</td>
                <td>${merek}</td>
                <td>${jumlah}</td>
                <td style="text-align: center">
                    <button class="btn btn-icon btn-danger btn-rounded remove-row">
                        <i class="anticon anticon-close"></i>
                    </button>
                </td>
                <input type="hidden" name="table_data[${rowCount}][kode_barang]" value="${kodeBarang}">
                <input type="hidden" name="table_data[${rowCount}][merek]" value="${merek}">
                <input type="hidden" name="table_data[${rowCount}][jumlah]" value="${jumlah}">
            </tr>`;

            rowCount++;

            const totalPembelianRow = tableBody.querySelector('tr:last-child');
            totalPembelianRow.insertAdjacentHTML('beforebegin', newRow);

            resetForm();
            setRemoveRowEvent();
            updateRowNumbers();
            updateTotal();
            kodeBarangInput.focus();
        });

        function resetForm() {
            kodeBarangInput.value = '';
            merekInput.value = '';
            jumlahInput.value = '';
        }

        function setRemoveRowEvent() {
            const removeButtons = document.querySelectorAll('.remove-row');
            removeButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    button.closest('tr').remove();
                    updateRowNumbers();
                    updateTotal();
                });
            });
        }

        function updateRowNumbers() {
            const tableBody = document.querySelector('table tbody');
            const rows = tableBody.querySelectorAll('tr:not(:last-child)');
            rowCount = 0;
            rows.forEach((row, index) => {
                const inputs = row.querySelectorAll('input');
                row.querySelector('td:first-child').innerText = index + 1;
                inputs.forEach(input => {
                    const inputName = input.name.replace(/\[\d+\]/, `[${index}]`);
                    input.setAttribute('name', inputName);
                });
                rowCount++;
            });
        }

        function updateTotal() {
            const tableBody = document.querySelector('table tbody');
            const rows = tableBody.querySelectorAll('tr:not(:last-child)');
            let total = 0;

            rows.forEach(row => {
                const jumlahCell = row.querySelector('td:nth-child(4)');
                if (jumlahCell) {
                    total += parseInt(jumlahCell.textContent) || 0;
                }
            });

            const totalCell = document.getElementById('total-cell');
            const totalInput = document.getElementById('total_jumlah');
            if (totalCell) {
                totalCell.textContent = total;
                totalInput.value = total;
            }
        }
    });
</script>

<script>
    $(document).on('click', '.btn-import', function(e) {
        e.preventDefault();
        let url = "/modal-import-gabungkan";
        $(this).prop('disabled', true)
        $.ajax({
            url,
            type: "GET",
            dataType: "HTML",
            success: function(data) {
                $('#importmodal').html(data);
                $('#importmodal').modal('show');
                $('.btn-import').prop("disabled", false);
                $('.btn-import').html('<span>Import</span>');
            },
            error: function(error) {
                console.error(error);
                $('.btn-import').prop('disabled', false);
                $('.btn-import').html('<span>Import</span>');
            }
        })
    })
</script>

<script>

    $(document).on('submit', '#form-imporbarang', function(e) {
        e.preventDefault();

        const fileInput = $('#customFile')[0];
        const file = fileInput.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const data = event.target.result;
                const parsedData = parseCSV(data);
                importToTable(parsedData);
                fileInput.value = '';
                $('.modal').modal('hide');
            };
            reader.readAsText(file);
        } else {
            alert("Silakan pilih file terlebih dahulu.");
        }
    });

    let rowCount = 0;

    function importToTable(data) {
        const tableBody = document.querySelector('table tbody');

        data.forEach(item => {
            const newRow = `
            <tr>
                <td></td>
                <td>${item.kode_barang}</td>
                <td>${item.merek}</td>
                <td>${item.jumlah}</td>
                <td style="text-align: center">
                    <button class="btn btn-icon btn-danger btn-rounded remove-row">
                        <i class="anticon anticon-close"></i>
                    </button>
                </td>
                <input type="hidden" name="table_data[${rowCount}][kode_barang]" value="${item.kode_barang}">
                <input type="hidden" name="table_data[${rowCount}][merek]" value="${item.merek}">
                <input type="hidden" name="table_data[${rowCount}][jumlah]" value="${item.jumlah}">
            </tr>`;

            const totalPembelianRow = tableBody.querySelector('tr:last-child');
            totalPembelianRow.insertAdjacentHTML('beforebegin', newRow);

        });

        updateRowNumbers();
        setRemoveRowEvent();
        updateTotal();
    }

    function parseCSV(data) {
        const lines = data.split('\n');
        const result = [];
        for (let i = 1; i < lines.length; i++) {
            if (lines[i].trim() === '') continue;
            const columns = lines[i].split(',');
            result.push({
                kode_barang: columns[0]?.trim() || '',
                merek: columns[1]?.trim() || '',
                jumlah: parseInt(columns[2], 10) || 0
            });
        }
        return result;
    }

    function setRemoveRowEvent() {
        const removeButtons = document.querySelectorAll('.remove-row');
        removeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const row = button.closest('tr');
                row.remove();
                updateRowNumbers();
                updateTotal();
            });
        });
    }

        function updateRowNumbers() {
            const tableBody = document.querySelector('table tbody');
            const rows = tableBody.querySelectorAll('tr:not(:last-child)');
            rowCount = 0;
            rows.forEach((row, index) => {
                const inputs = row.querySelectorAll('input');
                row.querySelector('td:first-child').innerText = index + 1;
                inputs.forEach(input => {
                    const inputName = input.name.replace(/\[\d+\]/, `[${index}]`);
                    input.setAttribute('name', inputName);
                });
                rowCount++;
            });
        }

        function updateTotal() {
            const tableBody = document.querySelector('table tbody');
            const rows = tableBody.querySelectorAll('tr:not(:last-child)');
            let total = 0;

            rows.forEach(row => {
                const jumlahCell = row.querySelector('td:nth-child(4)');
                if (jumlahCell) {
                    total += parseInt(jumlahCell.textContent) || 0;
                }
            });

            const totalCell = document.getElementById('total-cell');
            const totalInput = document.getElementById('total_jumlah');
            if (totalCell) {
                totalCell.textContent = total;
                totalInput.value = total;
            }
        }

</script>


@endpush
