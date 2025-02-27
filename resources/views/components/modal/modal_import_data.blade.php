<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title h4">{{$title}}</h5>
        </div>
        <div class="modal-body">
            <form action="{{$action}}" method="POST" enctype="multipart/form-data" id="form-imporbarang">
                @csrf
                <div class="form-group">
                    <label for="formGroupExampleInput2">Import Data with file</label>
                    <div class="custom-file mb-3">
                        <input type="file" class="custom-file-input" id="customFile" name="customFile" onchange="updateFileName()">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                    @switch($type)
                        @case('setharga')
                            <a href="{{route('download-tamplate-setharga')}}" style="color: rgb(0, 168, 0); margin-top: 10px" >click here to download tamplate</a>
                            @break
                        @case('barang')
                            <a href="{{route('barang.download-tamplate-barang')}}" style="color: rgb(0, 168, 0); margin-top: 10px" >click here to download tamplate</a>
                            @break
                        @case('detailPembelian')
                            <a href="{{route('download-tamplate-detail-pembelian')}}" style="color: rgb(0, 168, 0); margin-top: 10px" >click here to download tamplate</a>
                            @break
                        @case('detailPenjualan')
                            <a href="{{route('download-tamplate-detail-penjualan')}}" style="color: rgb(0, 168, 0); margin-top: 10px" >click here to download tamplate</a>
                            @break
                        @case('detailGabungkan')
                            <a href="{{route('download-tamplate-detail-gabungkan')}}" style="color: rgb(0, 168, 0); margin-top: 10px" >click here to download tamplate</a>
                            @break
                        @break

                        @default
                    @endswitch
                </div>
                <button class="btn btn-primary" type="submit" id="savefile">Upload</button>
            </form>
        </div>
    </div>
</div>

