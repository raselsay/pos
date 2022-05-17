@extends('layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Notification</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ URL::to('/home') }}">Home</a></li>
                        <li class="breadcrumb-item">Bank</li>
                        <li class="breadcrumb-item active">Notification</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <div class="container">
        <div class="card m-0">
            <div class="card-header pt-3  flex-row align-items-center justify-content-between">
                <h5 class="m-0 font-weight-bold">Notification</h5>
            </div>
            <div class="card-body px-3 px-md-5">
                {{-- datatable start --}}
                <div class="table-responsive mt-2">
                    <table
                        class="table table-sm table-bordered table-striped align-items-center display table-flush data-table text-center">
                        <thead class="thead-light">
                            <tr>
                                <th>No.</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                {{-- datatable end --}}
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }

        });
        $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ URL::to('/admin/notification') }}"
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'details',
                    name: 'details',
                },
            ]
        });

        function ModalClose() {
            $('input').val('');
            $("select option[value='']").attr('selected', true);
            $('.invalid-feedback').hide();
            $('input').css('border', '1px solid rgb(209,211,226)');
            $('select').css('border', '1px solid rgb(209,211,226)');
        }

    </script>
@endsection
