@extends('layouts.app')

@section('content')
<div class="card p-3">
    <!-- Top Row: All Controls Aligned to the Right -->
    <div class="d-flex justify-content-end align-items-center mb-2">
        <!-- File Upload/Update Form -->
        <form action="{{ route('documents.updateFile', $document->id) }}" method="POST" enctype="multipart/form-data" class="mb-0 me-2">
            @csrf
            @method('PUT')
            <div class="input-group input-group-sm">
                <input type="file" name="file" class="form-control form-control-sm" required>
                <button type="submit" class="btn btn-info btn-sm">
                    @if ($document->file_path)
                        Update Document
                    @else
                        Upload Document
                    @endif
                </button>
            </div>
        </form>

        <!-- Show PDF Button or No File Message -->
        <div class="me-2">
            @if ($document->file_path)
                <button class="btn btn-primary btn-sm" onclick="togglePDF()">Show PDF</button>
            @else
                <span class="text-danger">No file available</span>
            @endif
        </div>

        <!-- Print Button -->
        <button class="btn btn-success btn-sm me-2" onclick="printDetails()">Print</button>
        <!-- Back Button -->
        <a href="{{ route('documents.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <!-- Print Section -->
    <div id="printSection">
        <h3>{{ $document->name }}</h3>
        <p><strong>Category:</strong> {{ $document->category }}</p>
        <p><strong>Br/Off/Unit:</strong> {{ $document->drafter }}</p>

        <!-- Added Creator and Last Updated By -->
        <p><strong>Created By:</strong> {{ $document->creator->name ?? 'Unknown' }} on {{ $document->created_at->format('Y-m-d H:i') }}</p>
        <p><strong>Last Updated By:</strong> {{ $document->updater->name ?? 'Unknown' }} on {{ $document->updated_at->format('Y-m-d H:i') }}</p>

        <h4>Routing History:</h4>

        @php
            // Sort locations by timestamp (earliest first)
            $sortedLocations = $document->locations->sortBy('timestamp');
        @endphp

        @if ($sortedLocations->isNotEmpty())
            <table class="table table-bordered">
                <colgroup>
                    <col style="width: 5%;">   <!-- # -->
                    <col style="width: 20%;">  <!-- Br/Off/Unit -->
                    <col style="width: 20%;">  <!-- Received By -->
                    <col style="width: 10%;">  <!-- Date and Time -->
                    <col style="width: 20%;">  <!-- Logs (Smaller Column) -->
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Br/Off/Unit</th>
                        <th>Received By</th>
                        <th>Date and Time</th>
                        <th>Logs</th> <!-- New Column for Creator and Updater Logs -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sortedLocations as $index => $location)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $location->location }}</td>
                            <td>{{ $location->receiver }}</td>
                            <td>{{ \Carbon\Carbon::parse($location->timestamp)->format('Y-m-d H:i') }}</td>
                            <td>
                                <strong>Created By: </strong> {{ $location->creator->name ?? 'Unknown' }}<br>
                                <small>on {{ $location->created_at->format('Y-m-d H:i') }}</small><br>
                                <strong>Last Updated By: </strong> {{ $location->updater->name ?? 'Unknown' }}<br>
                                <small>on {{ $location->updated_at->format('Y-m-d H:i') }}</small>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-center text-muted">No routing history available</p>
        @endif
    </div>

    <!-- PDF Viewer (Hidden by Default) -->
    @if ($document->file_path)
        <div id="pdfViewer" style="display: none;">
            <iframe src="{{ asset('storage/' . $document->file_path) }}" width="100%" height="600px"></iframe>
        </div>
    @endif
</div>

<!-- JavaScript for Print and PDF Toggle -->
<script>
    function togglePDF() {
        let pdfViewer = document.getElementById("pdfViewer");
        let button = document.querySelector(".btn-primary");
        if (pdfViewer.style.display === "none") {
            pdfViewer.style.display = "block";
            button.textContent = "Hide PDF";
        } else {
            pdfViewer.style.display = "none";
            button.textContent = "Show PDF";
        }
    }

    function printDetails() {
        let printContent = document.getElementById("printSection").innerHTML;
        let originalContent = document.body.innerHTML;

        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
        location.reload();
    }
</script>
@endsection
