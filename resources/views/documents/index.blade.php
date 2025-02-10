@extends('layouts.app')
@section('content')
<style>
/* Responsive Table */
.table-responsive {
    overflow-x: auto;
    display: block;
    width: 100%;

}

.table-bordered {
  border-collapse: collapse;
  border: none;
}

.table-bordered th,
.table-bordered td {
  border: none;
  border-bottom: 1px solid black;
}

#dataTable {
  width: 100%;
  max-width: 100%;
  /* Optionally, you can use table-layout: auto; (or fixed) depending on your design */
}

@media (max-width: 400px) {
  /* Make sure the table container takes full width and scrolls horizontally if needed */
  .table-responsive {
    width: 100% !important;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
  }
  
  #dataTable {
    width: 100% !important;
    max-width: 100% !important;
  }
}


@media (max-width: 768px) {
    .btn {
        width: 100%; /* Buttons take full width on mobile */
        margin-bottom: 5px;
    }
}

    #mobileMessage {
      display: none;
    }
    /* Display the message for screens with a max width of 767px (adjust as needed) */
    @media only screen and (max-width: 767px) {
      #mobileMessage {
        display: block;
      }
    }
</style>
      @if(Auth::user()->role != 'viewer')

<div class="card p-3 mb-3 form">
    <form id="addDocumentForm" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-4 col-12 mb-3">
                <label for="name" class="form-label text-primary">Document Name / Subject</label>
                <input type="text" name="name" class="form-control" placeholder="Document Name" required>
            </div>
            <div class="col-md-2 col-12 mb-3">
                <label for="drafter" class="form-label text-primary">Originator</label>
                <input type="text" name="drafter" class="form-control" placeholder="From/Origin" required>
            </div>
            <div class="col-md-1 col-12 mb-3">
                <label for="category" class="form-label text-primary">Category</label>
                <select name="category" class="form-control">
                    <option value="Incoming">Incoming</option>
                    <option value="Outgoing">Outgoing</option>
                </select>
            </div>
            <div class="col-md-1 col-12 mb-3">
                <label for="purpose" class="form-label text-primary">Purpose</label>
                <select name="purpose" class="form-control" id="purposeSelect">
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <label for="timestamp" class="form-label text-primary">Date Received / Sent</label>
                <input label="Date Received"type="datetime-local" name="date_rcvd_sent" class="form-control" required>
            </div>
            <div class="col-md-2 col-12 mb-3">
                <label for="category" class="form-label text-primary">File (Scanned PDF)</label>
                <input type="file" name="file" class="form-control">
            </div>
        </div>

        <h5 class="mt-3">Initial Routing History</h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="location" class="form-label text-primary">Dispatched To</label>
                <input type="text" name="location" class="form-control" placeholder="Br/Off/Unit" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="receiver" class="form-label text-primary">Received By</label>
                <input type="text" name="receiver" class="form-control" placeholder="Received By" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="receiver" class="form-label text-primary">Dispatched By</label>
                <input type="text" name="dispatcher" class="form-control" placeholder="Dispatched By" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="timestamp" class="form-label text-primary">Date / Time</label>
                <input type="datetime-local" name="timestamp" class="form-control" required>
            </div>
        </div>
        <div class="row">
          <div class="col d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Add Document</button>
          </div>
        </div>

    </form>
</div>
                    @endif

    <!-- Filter Input -->
<div class="row mb-2 justify-content-end" style="text-align: center;">
  
  <div class="col-md-3 m-1 col-12">
    <input type="text" id="tableFilter" class="form-control" placeholder="Type to filter...">
  </div>
  <div class="col-md-3 m-1  col-12">
    <div class="input-group">
      <input type="date" id="dateFilter" class="form-control">
      <button class="btn btn-outline-secondary" type="button" id="clearDateFilter">Clear Date</button>
    </div>
  </div>
  <div class="col-md-3 m-1  col-12">
    <select id="categoryFilter" class="form-control">
      <option value="">All</option>
      <option value="Incoming">Incoming</option>
      <option value="Outgoing">Outgoing</option>
    </select>
  </div>
  <div class="col-md-2 m-1 justify-content-end col-12" style="margin-top: -3px;padding-right: 0px;text-align: right;padding-right: 10px;">
    <button id="exportExcel" name="export" class="btn btn-secondary mb-1">Export to Excel</button>
  </div>
</div>


      <div class="row text-center" id="mobileMessage" style="font-size: 12px;color: gray;">
        <small >Swipe / Scroll Right to View More...</small>
      </div>
    <div class="table-responsive">
    <table class="table table-bordered" id="dataTable" >
        <thead>
            <tr>
                <th style="width: 7%;" rowspan="2">Date</th>
                <th style="width: 5%;" rowspan="2">Category</th>
                <th style="width: 20%;"rowspan="2">Document Name</th>
                <th style="width: 10%;" rowspan="2">Originator</th>
                <th style="width: 10%;" rowspan="2">Purpose</th>
                <th style="" colspan="5" class="text-center">Routing History</th>
                <th  rowspan="2" style="text-align: right;">Document Actions</th>
            </tr>
            <tr>
                <th>TO: Br/Off/Unit</th>
                <th>Received By</th>
                <th>Dispatched By</th>
                <th >Date and Time</th>
                @if(Auth::user()->role != 'viewer')
                 <th style="text-align: right;">Routing Actions</th>
                @endif

            </tr>
        </thead>
        <tbody>
            @foreach ($documents as $document)
@php
    $rowspan = max(1, $document->locations->count());
@endphp

@if ($document->locations->isEmpty())
    <tr data-document-id="{{ $document->id }}">
        <td rowspan="{{ $rowspan }}" class="document-detail" data-field="date_rcvd_sent">{{ $document->date_rcvd_sent?$document->date_rcvd_sent:' - ' }}</td>
        <td rowspan="{{ $rowspan }}" class="document-detail" data-field="category">{{ $document->category }}</td>
        <td rowspan="{{ $rowspan }}" class="document-detail" data-field="name">{{ $document->name }}</td>
        <td rowspan="{{ $rowspan }}" class="document-detail" data-field="drafter">{{ $document->drafter }}</td>
        <td rowspan="{{ $rowspan }}" class="document-detail" data-field="purpose">{{ $document->purpose }}</td>
        <td colspan="5" class="text-center text-muted">No routing history available</td>
        <td rowspan="{{ $rowspan }}" style="text-align: right;">
            <a href="{{ route('documents.show', $document->id) }}" class="btn btn-info btn-sm">View</a>
            <!-- NEW: Inline edit buttons for document details -->
            @if(Auth::user()->role != 'viewer')
            <button class="btn btn-warning btn-sm editDocumentBtn">Edit</button>
            <button class="btn btn-success btn-sm saveDocumentBtn d-none">Save</button>
            <button class="btn btn-secondary btn-sm cancelDocumentBtn d-none">Cancel</button>
            <!-- End NEW -->
            <button class="btn btn-success btn-sm addRoutingBtn"  onclick="openModal('addRoutingModal-','{{ $document->id }}')" >Route</button>
            <form action="{{ route('documents.destroy', $document->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this document?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
            @endif

        </td>
    </tr>
@else
    @foreach ($document->locations as $index => $location)
        <tr data-document-id="{{ $document->id }}" data-id="{{ $location->id }}">
            @if ($index === 0)
                <td rowspan="{{ $rowspan }}" class="document-detail" data-field="date_rcvd_sent">{{ $document->date_rcvd_sent?$document->date_rcvd_sent:' - ' }}</td>
                <td rowspan="{{ $rowspan }}" class="document-detail" data-field="category">{{ $document->category }}</td>
                <td rowspan="{{ $rowspan }}" class="document-detail" data-field="name">{{ $document->name }}</td>
                <td rowspan="{{ $rowspan }}" class="document-detail" data-field="drafter">{{ $document->drafter }}</td>
                <td rowspan="{{ $rowspan }}" class="document-detail" data-field="purpose">{{ $document->purpose }}</td>
                <!-- Routing history cells for the first row -->
                <td class="editable routing-history" data-field="location">{{ $location->location }}</td>
                <td class="editable" data-field="receiver">{{ $location->receiver }}</td>
                <td class="editable" data-field="dispatcher">{{ $location->dispatcher }}</td>
                <td class="editable" data-field="timestamp">{{ \Carbon\Carbon::parse($location->timestamp)->format('Y-m-d H:i') }}</td>
                <td style="text-align: right;">
                    @if(Auth::user()->role != 'viewer')
                    <button class="btn btn-warning btn-sm editRoutingBtn">Edit</button>
                    <button class="btn btn-success btn-sm saveRoutingBtn d-none">Save</button>
                    <button class="btn btn-secondary btn-sm cancelRoutingBtn d-none">Cancel</button>
                    <button class="btn btn-danger btn-sm deleteRoutingBtn" data-id="{{ $location->id }}">Delete</button>
                    @endif

                </td>
                <td rowspan="{{ $rowspan }}" style="text-align: right;">
                    <a href="{{ route('documents.show', $document->id) }}" class="btn btn-info btn-sm">View</a>
                    <!-- NEW: Inline edit buttons for document details -->
      @if(Auth::user()->role != 'viewer')
                    <button class="btn btn-warning btn-sm editDocumentBtn">Edit</button>
                    <button class="btn btn-success btn-sm saveDocumentBtn d-none">Save</button>
                    <button class="btn btn-secondary btn-sm cancelDocumentBtn d-none">Cancel</button>
                    <!-- End NEW -->
                    <button class="btn btn-success btn-sm addRoutingBtn"   onclick="openModal('addRoutingModal-','{{ $document->id }}')">Route</button>
                    <form action="{{ route('documents.destroy', $document->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this document?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    @endif


                </td>
            @else
                <td class="editable" data-field="location">{{ $location->location }}</td>
                <td class="editable" data-field="receiver">{{ $location->receiver }}</td>
                <td class="editable" data-field="dispatcher">{{ $location->dispatcher }}</td>
                <td class="editable" data-field="timestamp">{{ \Carbon\Carbon::parse($location->timestamp)->format('Y-m-d H:i') }}</td>
                <td style="text-align: right;">
      @if(Auth::user()->role != 'viewer')

                    <button class="btn btn-warning btn-sm editRoutingBtn">Edit</button>
                    <button class="btn btn-success btn-sm saveRoutingBtn d-none">Save</button>
                    <button class="btn btn-secondary btn-sm cancelRoutingBtn d-none">Cancel</button>
                    <button class="btn btn-danger btn-sm deleteRoutingBtn" data-id="{{ $location->id }}">Delete</button>
                    @endif

                </td>
            @endif
        </tr>
    @endforeach
@endif


                <!-- Route Modal -->
                <div class="modal fade" id="addRoutingModal-{{ $document->id }}" tabindex="-1" aria-labelledby="addRoutingModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('locations.store') }}" method="POST" class="ajax-form">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Route</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="document_id" value="{{ $document->id }}">
                                    <div class="mb-3">
                                        <label>Br/Off/Unit</label>
                                        <input type="text" name="location" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Received By</label>
                                        <input type="text" name="receiver" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Dispatched By</label>
                                        <input type="text" name="dispatcher" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Date and Time</label>
                                        <input type="datetime-local" name="timestamp" class="form-control" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Edit Routing Modal -->
                @foreach ($document->locations as $location)
                <div class="modal fade" id="editRoutingModal-{{ $location->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('locations.update', $location->id) }}" method="POST" class="editRoutingForm">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Routing History</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="location_id" value="{{ $location->id }}">
                                    <div class="mb-3">
                                        <label>Br/Off/Unit</label>
                                        <input type="text" name="location" class="form-control" value="{{ $location->location }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Received By</label>
                                        <input type="text" name="receiver" class="form-control" value="{{ $location->receiver }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Date and Time</label>
                                        <input type="datetime-local" name="timestamp" class="form-control" value="{{ $location->timestamp }}" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary saveEditRoutingBtn">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            @endforeach
        </tbody>
    </table>
    </div>

<!-- Ensure Bootstrap JS is loaded -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>

var current_modal = '';
var current_document = '';
var userRole = @json(Auth::user()->role);
console.log(userRole)
    const purposes = [
      { value: "For Out", text: "For Out" },
      { value: "For Concurrence", text: "For Concurrence" },
      { value: "For Signature", text: "For Signature" },
      { value: "For Compliance", text: "For Compliance" },
      { value: "For Appropriate Action", text: "For Appropriate Action" },
      { value: "For Attendance", text: "For Attendance" },
      { value: "For Reference", text: "For Reference" },
      { value: "For Info", text: "For Info" },
      { value: "RWC", text: "RWC" },
      { value: "Others", text: "Others" }
    ];
    
    function openModal(modalname, id){
            current_modal = modalname+id;
            current_document = id;
            // Get the modal element by its ID
            var myModalEl = document.getElementById(current_modal);

            // Create a new Bootstrap Modal instance (or get the existing instance)
            var myModal = bootstrap.Modal.getInstance(myModalEl);
            if (!myModal) {
              myModal = new bootstrap.Modal(myModalEl);
            }
            // Show the modal
            myModal.show();
    }



document.addEventListener("DOMContentLoaded", function() {
    let dateInputs = document.querySelectorAll('input[type="datetime-local"]');
    let now = new Date();
    let formattedDate = now.getFullYear() + "-" +
        ("0" + (now.getMonth() + 1)).slice(-2) + "-" +
        ("0" + now.getDate()).slice(-2) + "T" +
        ("0" + now.getHours()).slice(-2) + ":" +
        ("0" + now.getMinutes()).slice(-2);
    dateInputs.forEach(input => {
        if (!input.value) {
            input.value = formattedDate;
        }
    });

    if(userRole!='viewer'){
        // Get the select element by its ID.
        const select = document.getElementById("purposeSelect");
        
        // Optionally clear out any existing content.
        select.innerHTML = "";
        
        // Loop over the purposes array and create an <option> element for each.
        purposes.forEach(item => {
          const option = document.createElement("option");
          option.value = item.value;
          option.textContent = item.text;
          select.appendChild(option);
        });
    }
});
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {

    // Delete Routing History with proper handling of the document row
$(document).on('click', '.deleteRoutingBtn', function (e) {
    e.preventDefault();
    let button = $(this);
    let row = button.closest('tr');
    let docId = row.attr('data-document-id');
    let id = button.attr('data-id');

    if (!id) {
        alert("Error: Unable to find location ID.");
        return;
    }
    if (!confirm("Are you sure you want to delete this routing history?")) {
        return;
    }

    $.ajax({
        url: `/locations/${id}`,
        type: "DELETE",
        data: { _token: "{{ csrf_token() }}" },
        success: function (response) {
            if (response.success) {
                alert("Routing history deleted successfully!");

                // Get all rows for this document.
                let docRows = $(`tr[data-document-id="${docId}"]`);
                if (docRows.length === 1) {
                    // This is the only routing row (the document row).
                    let documentRow = docRows.first();
                    let routingCell = documentRow.find('.routing-history');
                    // Remove the extra routing cells (the receiver, timestamp, and actions cells).
                    // We remove all sibling <td>s until we hit the cell with a rowspan (the document actions cell).
                    routingCell.nextUntil('td[rowspan]').remove();
                    // Update the routing cell to span all 4 routing columns.
                    routingCell.attr('colspan', 5)
           .addClass('text-center')
           .html('<span class="text-muted">No routing history available</span>');

                } else {
                    // More than one routing row exists.
                    // If the row being deleted is the document row (the first row), promote the next row.
                    let documentRow = docRows.first();
                    if ( row.is(documentRow) ) {
                        let newDocRow = docRows.eq(1);
                        // Copy the document cells (the first 4 cells) from the old document row.
                        let docInfoCells = documentRow.children('td').slice(0, 5).clone();
                        newDocRow.prepend(docInfoCells);
                        // Remove the old document row.
                        documentRow.remove();
                    } else {
                        // Simply remove the routing row.
                        row.remove();
                        // Update the rowspan on the document row.
                        let rowspanCell = documentRow.find('td[rowspan]');
                        if (rowspanCell.length) {
                            let newRowspan = parseInt(rowspanCell.attr('rowspan')) - 1;
                            if (newRowspan > 1) {
                                rowspanCell.attr('rowspan', newRowspan);
                            } else {
                                rowspanCell.removeAttr('rowspan');
                            }
                        }
                    }
                }
            } else {
                alert("Failed to delete routing history.");
            }
        },
        error: function (xhr) {
            alert("Error: " + (xhr.responseJSON?.message || "An error occurred."));
        }
    });
});


    // Other event handlers below remain unchanged...

    function attachDeleteHandlers() {
        document.querySelector("tbody").addEventListener("click", function (event) {
            if (event.target.classList.contains("delete-btn")) {
                let confirmDelete = confirm("Are you sure you want to delete this?");
                if (!confirmDelete) return;
                let id = event.target.getAttribute("data-id");
                $.ajax({
                    url: "/documents/" + id,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        if (response.success) {
                            alert("Deleted successfully!");
                            event.target.closest("tr").remove();
                        }
                    },
                    error: function (xhr) {
                        alert("Error: " + xhr.responseJSON.message);
                    }
                });
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        attachDeleteHandlers();
    });

    function updateTable(doc) {
        let tableBody = $("tbody");
        let rowspan = Math.max(1, doc.locations.length);
        let datee = new Date(doc.locations[0].timestamp);
        let formattedDate = datee.getFullYear() + "-" +
            ("0" + (datee.getMonth() + 1)).slice(-2) + "-" +
            ("0" + datee.getDate()).slice(-2) + " " +
            ("0" + datee.getHours()).slice(-2) + ":" +
            ("0" + datee.getMinutes()).slice(-2);
        let newRow = $("<tr>").html(`
            <td rowspan="${rowspan}" class="document-detail" data-field="date_rcvd_sent">${doc.date_rcvd_sent}</td>
            <td rowspan="${rowspan}" class="document-detail" data-field="category">${doc.category}</td>
            <td rowspan="${rowspan}" class="document-detail" data-field="name">${doc.name}</td>
            <td rowspan="${rowspan}" class="document-detail" data-field="drafter">${doc.drafter}</td>
            <td rowspan="${rowspan}" class="document-detail" data-field="purpose">${doc.purpose}</td>
            ${doc.locations.length > 0 ? `
                <td  class="editable" data-field="location">${doc.locations[0].location}</td>
                <td  class="editable" data-field="receiver">${doc.locations[0].receiver}</td>
                <td  class="editable" data-field="dispatcher">${doc.locations[0].dispatcher}</td>
                <td  class="editable" data-field="timestamp">${formattedDate}</td>
                <td style="text-align: right;">
                    <button class="btn btn-warning btn-sm editRoutingBtn">Edit</button>
                    <button class="btn btn-success btn-sm saveRoutingBtn d-none">Save</button>
                    <button class="btn btn-secondary btn-sm cancelRoutingBtn d-none">Cancel</button>
                    <button class="btn btn-danger btn-sm deleteRoutingBtn" data-id="${doc.locations[0].id }">Delete</button>
                </td>
            ` : `<td colspan="5" class="text-center text-muted">No routing history available</td>`}
            <td rowspan="${rowspan}" style="text-align: right;">
                <a href="/documents/${doc.id}" class="btn btn-info btn-sm">View</a>
                <button class="btn btn-warning btn-sm editDocumentBtn">Edit</button>
                <button class="btn btn-success btn-sm saveDocumentBtn d-none">Save</button>
                <button class="btn btn-secondary btn-sm cancelDocumentBtn d-none">Cancel</button>
                <button class="btn btn-success btn-sm addRoutingBtn" data-bs-toggle="modal" data-bs-target="#addRoutingModal-${doc.id}">Route</button>
                <form action="/documents/${doc.id}" method="POST" class="delete-form" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this document?');">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${doc.id}">Delete</button>
                </form>
            </td>
        `);
        newRow.attr('data-document-id', `${doc.id}`);
        newRow.attr('data-id', `${doc.locations[0].id}`);

        tableBody.prepend(newRow);
        doc.locations.slice(1).forEach(function(location) {
            let datee = new Date(location.timestamp);
            let formattedDate = datee.getFullYear() + "-" +
                ("0" + (datee.getMonth() + 1)).slice(-2) + "-" +
                ("0" + datee.getDate()).slice(-2) + " " +
                ("0" + datee.getHours()).slice(-2) + ":" +
                ("0" + datee.getMinutes()).slice(-2);
            let historyRow = $("<tr>").html(`
                <td>${location.location}</td>
                <td>${location.receiver}</td>
                <td>${location.dispatcher}</td>
                <td>${formattedDate}</td>
                <td>
                    <button class="btn btn-warning btn-sm editRoutingBtn" data-bs-toggle="modal" data-bs-target="#editRoutingModal-${location.id}">Edit</button>
                    <form action="/locations/${location.id}" method="POST" class="delete-form" data-id="${location.id}" style="display:inline;">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${location.id}">Delete</button>
                    </form>
                </td>
            `);
            tableBody.append(historyRow);
        });
    }
    function sortDocumentsTable() {
        // Create an object to hold groups of rows keyed by document ID.
        let groups = {};

        // Loop through each row that has a data-document-id.
        $("#dataTable tbody tr[data-document-id]").each(function () {
            let docId = $(this).attr("data-document-id");
            if (!groups[docId]) {
                groups[docId] = [];
            }
            groups[docId].push(this);
        });

        // Convert the groups object into an array so we can sort it.
        let groupsArray = [];
        for (let docId in groups) {
            // Assume that the first row in each group is the document row.
            let groupRows = groups[docId];

            // Get the date text from the cell with data-field="date_rcvd_sent".
            let dateText = $(groupRows[0]).find('td[data-field="date_rcvd_sent"]').text().trim();
            
            // Create a Date object.
            let dateValue = new Date(dateText);
            groupsArray.push({ docId: docId, date: dateValue, rows: groupRows });
        }

        // Sort the groups by date.
        // (For ascending order—earliest dates first. Use b.date - a.date for descending order.)
        groupsArray.sort(function (a, b) {
            return b.date - a.date;
        });

        // Reattach the rows in sorted order.
        let tbody = $("#dataTable tbody");
        tbody.empty(); // Remove all existing rows.
        groupsArray.forEach(function (group) {
            group.rows.forEach(function (row) {
                tbody.append(row);
            });
        });
    }


    $('#addDocumentForm').submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: "{{ route('documents.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    alert("Document added successfully!");
                    updateTable(response.document);
                    sortDocumentsTable();
                    $('#addDocumentForm')[0].reset();
                    let dateInputs = document.querySelectorAll('input[type="datetime-local"]');
                    let now = new Date();
                    let formattedDate = now.getFullYear() + "-" +
                        ("0" + (now.getMonth() + 1)).slice(-2) + "-" +
                        ("0" + now.getDate()).slice(-2) + " " +
                        ("0" + now.getHours()).slice(-2) + ":" +
                        ("0" + now.getMinutes()).slice(-2);
                    dateInputs.forEach(input => {
                        if (!input.value) {
                            input.value = formattedDate;
                        }
                    });
                }
            },
            error: function (xhr) {
                    console.log(xhr);
                    alert("Something's wrong")
            }
        });
    });




// $('#addDocumentForm').submit(function(e) {
//     e.preventDefault();

//     // Collect additional form fields into an object
//     let formDataFields = {
//         name: $('input[name="name"]').val(),
//         drafter: $('input[name="drafter"]').val(),
//         category: $('select[name="category"]').val(),
//         purpose: $('select[name="purpose"]').val(),
//         location: $('input[name="location"]').val(),
//         receiver: $('input[name="receiver"]').val(),
//         timestamp: $('input[name="timestamp"]').val(),
//         _token: "{{ csrf_token() }}"
//     };

//     // Check if a file is selected
//     let fileInput = $('input[name="file"]')[0];
//     if (!fileInput.files.length) {
//         // No file selected, proceed without file uploading
//         $.ajax({
//             url: "{{ route('documents.upload_chunk') }}", // Use same endpoint or adjust as needed
//             type: "POST",
//             data: JSON.stringify(formDataFields),
//             processData: false,
//             contentType: "application/json",
//             success: function(response) {
//                 if (response.success) {
//                     alert("Document added successfully!");
//                     updateTable(response.document);
//                     $('#addDocumentForm')[0].reset();
//                     let dateInputs = document.querySelectorAll('input[type="datetime-local"]');
//                     let now = new Date();
//                     let formattedDate = now.getFullYear() + "-" +
//                         ("0" + (now.getMonth() + 1)).slice(-2) + "-" +
//                         ("0" + now.getDate()).slice(-2) + " " +
//                         ("0" + now.getHours()).slice(-2) + ":" +
//                         ("0" + now.getMinutes()).slice(-2);
//                     dateInputs.forEach(input => {
//                         if (!input.value) {
//                             input.value = formattedDate;
//                         }
//                     });
//                 }
//             },
//             error: function(xhr) {
//                 console.log(xhr);
//                 alert("Something's wrong");
//             }
//         });
//         return; // End execution here if no file is present
//     }

//     // If a file is selected, proceed with the chunked upload.
//     let file = fileInput.files[0];
//     let chunkSize = 128 * 1024; // 128KB per chunk
//     let totalSize = file.size;
//     let totalChunks = Math.ceil(totalSize / chunkSize);
//     let currentChunk = 0;

//     function uploadChunk() {
//         let start = currentChunk * chunkSize;
//         let end = Math.min(start + chunkSize, totalSize);
//         let blob = file.slice(start, end);
//         let reader = new FileReader();

//         reader.onload = function(e) {
//             // The result is a Base64-encoded data URL
//             let chunkData = e.target.result;

//             // Build payload with metadata for this chunk
//             let payload = {
//                 ...formDataFields,
//                 fileName: file.name,
//                 fileType: file.type,
//                 fileSize: totalSize,
//                 chunkData: chunkData,  // Base64 data URL for the chunk
//                 chunkIndex: currentChunk,
//                 totalChunks: totalChunks
//             };

//             $.ajax({
//                 url: "{{ route('documents.upload_chunk') }}", // Endpoint for chunk uploads
//                 type: "POST",
//                 data: JSON.stringify(payload),
//                 processData: false,
//                 contentType: "application/json",
//                 success: function(response) {
//                     console.log('Chunk ' + currentChunk + ' uploaded.');
//                     currentChunk++;
//                     if (currentChunk < totalChunks) {
//                         // Continue uploading the next chunk
//                         uploadChunk();
//                     } else {
//                         // All chunks uploaded; execute success block:
//                         if (response.success) {
//                             alert("Document added successfully!");
//                             updateTable(response.document);
//                             $('#addDocumentForm')[0].reset();
//                             let dateInputs = document.querySelectorAll('input[type="datetime-local"]');
//                             let now = new Date();
//                             let formattedDate = now.getFullYear() + "-" +
//                                 ("0" + (now.getMonth() + 1)).slice(-2) + "-" +
//                                 ("0" + now.getDate()).slice(-2) + " " +
//                                 ("0" + now.getHours()).slice(-2) + ":" +
//                                 ("0" + now.getMinutes()).slice(-2);
//                             dateInputs.forEach(input => {
//                                 if (!input.value) {
//                                     input.value = formattedDate;
//                                 }
//                             });
//                         }
//                     }
//                 },
//                 error: function(xhr) {
//                     console.error("Error uploading chunk " + currentChunk, xhr);
//                     alert("Error uploading file. Please try again.");
//                 }
//             });
//         };

//         reader.readAsDataURL(blob);
//     }

//     // Start the chunked upload process
//     uploadChunk();
// });


    // $("body").on("click", ".addRoutingModal", function () {
    //     alert();
    // });

function updateDocumentRowspan(docId) {
  // Select all rows that belong to this document.
  let docRows = $(`tr[data-document-id="${docId}"]`);
  let newRowspan = docRows.length + 1;
  
  // The document row is the first row in the group.
  let documentRow = docRows.first();
  
  // Update the rowspan on all cells that are part of the document details.
  // In your structure, these cells have a class "document-detail" and the last actions cell also uses rowspan.
  documentRow.find('td.document-detail').attr('rowspan', newRowspan);
  
  // Update the actions cell (if needed) that has the rowspan (assumed to be the last cell in the row)
  documentRow.find('td[rowspan]').not('.document-detail').attr('rowspan', newRowspan);
  
  console.log(`Updated rowspan for document ${docId} to ${newRowspan}`);
}

function updateRoutingTable(data, doc)
{

        let docRows = $('tr[data-document-id="'+doc+'"]');
        let datetimeLocalValue = data.timestamp.replace("T", " ");

          let newRoutingRow = `
            <tr data-document-id="${doc}" data-id="${data.id}">
              <td class="editable routing-history" data-field="location">${data.location}</td>
              <td class="editable" data-field="receiver">${data.receiver}</td>
              <td class="editable" data-field="dispatcher">${data.dispatcher}</td>
              <td class="editable" data-field="timestamp">${datetimeLocalValue}</td>
              <td style="text-align: right;">
                <button class="btn btn-warning btn-sm editRoutingBtn">Edit</button>
                <button class="btn btn-success btn-sm saveRoutingBtn d-none">Save</button>
                <button class="btn btn-secondary btn-sm cancelRoutingBtn d-none">Cancel</button>
                <button class="btn btn-danger btn-sm deleteRoutingBtn" data-id="${data.id}">Delete</button>
              </td>
            </tr>
          `;
          newRowspan = docRows.length + 1;
            let documentRow = docRows.first();
            documentRow.find('td.document-detail').attr('rowspan', newRowspan);
  documentRow.find('td[rowspan]').not('.document-detail').attr('rowspan', newRowspan);
           $(docRows.last()).after(newRoutingRow);
}


$("[id^='addRoutingModal-'] form").submit(function(e) {
    e.preventDefault();
    let form = $(this);
    let formData = new FormData(form[0]);

    $.ajax({
        url: form.attr("action"),
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                // Hide the modal that contains the form
                form[0].reset();

                form.closest('.modal').modal('hide');
                // Optionally, update your page content here without reloading
                updateRoutingTable(response.data, current_document)
            }
        },
        error: function(xhr) {
            alert("Error: " + (xhr.responseJSON.message || "An error occurred."));
        }
    });
});


// $('.ajax-form').submit(function(e) {
//     e.preventDefault();
//     let form = $(this);
//     let formData = new FormData(form[0]);
//     $.ajax({
//         url: form.attr("action"),
//         type: "POST",
//         data: formData,
//         processData: false,
//         contentType: false,
//         success: function(response) {
//             if (response.success) {
//                 alert("Routing history added successfully!");
//                 form.closest('.modal').modal('hide');
//             }
//         },
//         error: function(xhr) {
//             alert("Error: " + (xhr.responseJSON.message || "An error occurred."));
//         }
//     });
// });

    $("tbody").on("click", ".editRoutingBtn", function () {
        let row = $(this).closest("tr");
        row.find(".editable").each(function () {
            // Save the original text in a data attribute
            let originalText = $(this).text().trim();
            $(this).data("original", originalText);

            let field = $(this).data("field");
            if (field === "timestamp") {
                // Convert to datetime-local format (replace space with "T")
                let datetimeLocalValue = originalText.replace(" ", "T");
                $(this).html(`<input type="datetime-local" class="form-control" name="${field}" value="${datetimeLocalValue}">`);
            } else {
                $(this).html(`<input type="text" class="form-control" name="${field}" value="${originalText}">`);
            }
        });
        row.find(".editRoutingBtn").addClass("d-none");
        row.find(".saveRoutingBtn, .cancelRoutingBtn").removeClass("d-none");
    });

    $("tbody").on("click", ".cancelRoutingBtn", function () {
        let row = $(this).closest("tr");
        row.find(".editable").each(function () {
            // Retrieve the original text stored when editing started
            let originalText = $(this).data("original");
            let field = $(this).data("field");

            // If it's the timestamp field, no further conversion is needed because the original value was stored in its display format.
            // (If your original display format already has a space, it will be restored as-is.)
            $(this).text(originalText);
        });

        row.find(".saveRoutingBtn, .cancelRoutingBtn").addClass("d-none");
        row.find(".editRoutingBtn").removeClass("d-none");
    });

    $("tbody").on("click", ".saveRoutingBtn", function () {
        let row = $(this).closest("tr");
        let id = row.data("id");
        let updatedData = {
            _token: "{{ csrf_token() }}",
            location: row.find("input[name='location']").val(),
            receiver: row.find("input[name='receiver']").val(),
            dispatcher: row.find("input[name='dispatcher']").val(),
            timestamp: row.find("input[name='timestamp']").val(),
        };
        $.ajax({
            url: `/locations/${id}`,
            type: "PUT",
            data: updatedData,
            success: function (response) {
                if (response.success) {
                    alert("Routing history updated!");
                    row.find(".editable").each(function () {
                        let field = $(this).data("field");
                        let inputValue = $(this).find("input").val();
                        if (field === "timestamp") {
                            // Replace the "T" with a space for display
                            inputValue = inputValue.replace("T", " ");
                        }
                        $(this).text(inputValue);
                    });

                    row.find(".saveRoutingBtn, .cancelRoutingBtn").addClass("d-none");
                    row.find(".editRoutingBtn").removeClass("d-none");
                }
            },
            error: function (xhr) {
                alert("Update failed: " + xhr.responseJSON.message);
            }
        });
    });

  function filterDocuments() {
    // Get filter values.
    let textValue = $('#tableFilter').val().toLowerCase();
    let dateValue = $('#dateFilter').val(); // format: "YYYY-MM-DD"
    let categoryValue = $('#categoryFilter').val().toLowerCase();

    // Object to store processed document IDs.
    let processedDocs = {};

    // Loop through all rows that have a data-document-id.
    $('tr[data-document-id]').each(function(){
      let row = $(this);
      let docId = row.attr('data-document-id');

      // Only process each document group once.
      if (processedDocs[docId]) return;
      processedDocs[docId] = true;

      // Get all rows for this document.
      let docRows = $('tr[data-document-id="' + docId + '"]');
      // Assume the first row is the document row.
      let docRow = docRows.first();

      // --- Text Filter ---
      // Check if the document row's text contains the filter text.
      let docText = docRow.text().toLowerCase();
      let textMatch = docText.indexOf(textValue) > -1;

      // --- Date Filter ---
      let dateMatch = true;
      if (dateValue) {
        // Look for a cell with data-field="timestamp" in the document row.
        let dateCell = docRow.find('[data-field="date_rcvd_sent"]');
        if (dateCell.length > 0) {
          let cellText = dateCell.text().trim();
          // Assume the date is in the first 10 characters (YYYY-MM-DD).
          let cellDate = cellText.substr(0, 10);
          dateMatch = (cellDate === dateValue);
        } else {
          // If there is no timestamp cell, treat it as not matching.
          dateMatch = false;
        }
      }

      // --- Category Filter ---
      let categoryMatch = true;
      if (categoryValue) {
        // Assume the category is in the first cell of the document row.
        let category = docRow.find('td:eq(1)').text().trim().toLowerCase();
        categoryMatch = (category.indexOf(categoryValue) > -1);
      }

      // --- Combined Filtering ---
      if (textMatch && dateMatch && categoryMatch) {
        docRows.show();
      } else {
        docRows.hide();
      }
    });
  }

  // Attach event handlers for the filters.
  $('#tableFilter').on('keyup', filterDocuments);
  $('#dateFilter').on('change', filterDocuments);
  $('#categoryFilter').on('change', filterDocuments);

  // Clear date filter button
  $('#clearDateFilter').on('click', function(){
    $('#dateFilter').val('');
    filterDocuments();
  });
  $('#exportExcel').on('click', function(){
    // Clone the original table so that we can modify it without affecting the original.
    let tableClone = $('#dataTable').clone();

    // Remove the unwanted "Actions" columns from the header rows.
    tableClone.find('thead tr').each(function(index) {
      // For the first header row, remove the last cell (global Actions)
      if (index === 0) {
        $(this).find('th:last').remove();
        $(this).find('th:last').attr('colspan', 4);
      }
      // For the second header row, remove the last cell (Routing History Actions)
      else if (index === 1) {
            if(userRole!='viewer') {
                $(this).find('th:last').remove();
            }
      }
    });

    // Remove the unwanted "Actions" columns from the body rows.
    tableClone.find('tbody tr').each(function(){
      let $cells = $(this).children();
      // For rows with 9 cells (document rows)
      if ($cells.length >=9) {
        $cells.eq(10).remove(); // Remove global actions
        $cells.eq(9).remove(); // Remove routing history actions (after removal, indexes shift)
      }
      // For subsequent routing rows that have 4 cells:
      else if ($cells.length === 5) {
        $cells.eq(4).remove(); // Remove routing actions cell
      }
      else if ($cells.length === 7) {
        $cells.eq(5).attr('colspan',4); // Remove routing actions cell
        $cells.eq(6).remove(); // Remove routing actions cell
      }

    });

    // Convert the modified table to a workbook.
    let workbook = XLSX.utils.table_to_book(tableClone[0], { sheet: "Sheet1" });

    // Get a reference to the worksheet.
    let ws = workbook.Sheets["Sheet1"];

    // Example: Style the header row (first row).
    // We'll loop through the columns in the first row (assuming columns A through H)
    // and apply a bold font, centered alignment, and a fill color.
    for (let col = 65; col <= 72; col++) {  // ASCII codes for A to H
      let cellRef = String.fromCharCode(col) + "1"; // e.g., A1, B1, ...
      if (ws[cellRef]) {
        ws[cellRef].s = {
          font: { bold: true, color: { rgb: "FFFFFF" } },   // White bold font
          fill: { patternType: "solid", fgColor: { rgb: "4F81BD" } }, // Blue fill
          alignment: { horizontal: "center", vertical: "center" }
        };
      }
    }

    // Optionally, you can add other styles to other cells as needed.
    // For example, you might want to set a border on all cells.
    // (Note: Advanced styling options are available only in the Pro version.)

    // Finally, write the workbook to an Excel file with styles enabled.
    XLSX.writeFile(workbook, 'export.xlsx', { cellStyles: true });
  });

// Inline editing for document details with dropdowns for Category and Purpose
$("tbody").on("click", ".editDocumentBtn", function () {
    let docRow = $(this).closest("tr");

    // Iterate over each document detail cell and store the original content.
    docRow.find(".document-detail").each(function () {
        // Save the original text so that we can restore it later
        let currentText = $(this).text().trim();
        $(this).data("originalText", currentText);

        let field = $(this).data("field");

        if (field === "category") {
            // Define dropdown options for Category
            let options = `
                <option value="Incoming" ${currentText === "Incoming" ? "selected" : ""}>Incoming</option>
                <option value="Outgoing" ${currentText === "Outgoing" ? "selected" : ""}>Outgoing</option>
            `;
            $(this).html(`<select class="form-control" name="${field}">${options}</select>`);
        } else if (field === 'date_rcvd_sent') {
            // Convert the current text into the proper datetime-local format
            let datetimeLocalValue = currentText.replace(" ", "T");
            $(this).html(`<input type="datetime-local" class="form-control" name="${field}" value="${datetimeLocalValue}">`);
        } else if (field === "purpose") {
            // Define dropdown options for Purpose
            // Generate the option HTML using the array and currentText for selection.
            let options = purposes.map(function(item) {
              // If currentText exactly equals the option value, add "selected" attribute.
              console.log(item);
              console.log(currentText);
              return `<option value="${item.value}" ${currentText === item.value ? "selected" : ""}>${item.text}</option>`;
            }).join("");

            // Replace the current element's HTML with the generated select element.
            $(this).html(`<select class="form-control" name="${field}">${options}</select>`);
        } else {
            // For other fields, use a text input.
            $(this).html(`<input type="text" class="form-control" name="${field}" value="${currentText}">`);
        }
    });

    // Toggle buttons: hide Edit, show Save/Cancel
    docRow.find(".editDocumentBtn").addClass("d-none");
    docRow.find(".saveDocumentBtn, .cancelDocumentBtn").removeClass("d-none");
});


$("tbody").on("click", ".cancelDocumentBtn", function () {
    let docRow = $(this).closest("tr");

    // Revert each cell to its original content.
    docRow.find(".document-detail").each(function () {
        let originalValue = $(this).data("originalText");
        $(this).html(originalValue);
    });

    // Toggle buttons: hide Save/Cancel, show Edit
    docRow.find(".saveDocumentBtn, .cancelDocumentBtn").addClass("d-none");
    docRow.find(".editDocumentBtn").removeClass("d-none");
});


$("tbody").on("click", ".saveDocumentBtn", function () {
    let docRow = $(this).closest("tr");
    let docId = docRow.data("document-id");

    // Collect the updated values from the input fields and selects.
    let updatedData = {
        _token: "{{ csrf_token() }}",
        category: docRow.find("select[name='category']").val() || docRow.find("input[name='category']").val(),
        name: docRow.find("input[name='name']").val(),
        drafter: docRow.find("input[name='drafter']").val(),
        date_rcvd_sent: docRow.find("input[name='date_rcvd_sent']").val(),
        purpose: docRow.find("select[name='purpose']").val() || docRow.find("input[name='purpose']").val(),
    };

    // Send an AJAX request to update the document.
    $.ajax({
        url: `/documents/${docId}`, // Ensure you have defined a PUT route for this endpoint.
        type: "PUT",
        data: updatedData,
        success: function (response) {
            if (response.success) {
                // Update each document detail cell with the new value.
                docRow.find(".document-detail").each(function () {
                    let field = $(this).data("field");
                    let newValue = '';
                    // For dropdowns, get the selected text.
                    if(field==='date_rcvd_sent'){
                        newValue = updatedData[field].replace("T", " ")
                    }else{
                        newValue = (field === "category" || field === "purpose")
                            ? $(this).find("select option:selected").text()
                            : updatedData[field];
                    }
                    $(this).html(newValue);
                });
                // Toggle buttons: hide Save/Cancel, show Edit.
                docRow.find(".saveDocumentBtn, .cancelDocumentBtn").addClass("d-none");
                docRow.find(".editDocumentBtn").removeClass("d-none");
                alert("Document details updated successfully!");
            } else {
                alert("Failed to update document details.");
            }
        },
        error: function (xhr) {
            alert("Error updating document: " + (xhr.responseJSON.message || "An error occurred."));
        }
    });
});


});
</script>
@endsection
