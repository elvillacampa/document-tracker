@extends('layouts.app')
@section('content')
<style>
.table-bordered {
  border-collapse: collapse;
  border: none;
}

.table-bordered th,
.table-bordered td {
  border: none;
  border-bottom: 1px solid black;
}
</style>
@section('content')

<div class="card p-3 mb-3">
    <form id="addDocumentForm" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-3 mb-3">
                <input type="text" name="name" class="form-control" placeholder="Document Name" required>
            </div>
            <div class="col-md-3 mb-3">
                <input type="text" name="drafter" class="form-control" placeholder="From/Origin" required>
            </div>
            <div class="col-md-3 mb-3">
                <select name="category" class="form-control">
                    <option value="Incoming">Incoming</option>
                    <option value="Outgoing">Outgoing</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <select name="purpose" class="form-control">
                      <option value="For Signature">For Signature</option>
                      <option value="For Route">For Route</option>
                      <option value="RWC">RWC</option>
                      <option value="For Appropriate Action">For Appropriate Action</option>
                      <option value="For Info">For Info</option>
                      <option value="For Reference">For Reference</option>
                      <option value="For Concurrence">For Concurrence</option>
                      <option value="Others">Others</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <input type="file" name="file" class="form-control">
            </div>
        </div>

        <h5 class="mt-3">Initial Routing History</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <input type="text" name="location" class="form-control" placeholder="Br/Off/Unit" required>
            </div>
            <div class="col-md-4 mb-3">
                <input type="text" name="receiver" class="form-control" placeholder="Received By" required>
            </div>
            <div class="col-md-4 mb-3">
                <input type="datetime-local" name="timestamp" class="form-control" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Add Document</button>
    </form>
</div>
    <!-- Filter Input -->
    <div class="row mb-3">
      <div class="col-md-4">
        <label for="tableFilter" class="form-label">Filter Table:</label>
        <input type="text" id="tableFilter" class="form-control" placeholder="Type to filter...">
      </div>
      <div class="col-md-4">
        <label for="dateFilter" class="form-label">Filter by Date:</label>
        <div class="input-group">
          <input type="date" id="dateFilter" class="form-control">
          <button class="btn btn-outline-secondary" type="button" id="clearDateFilter">Clear</button>
        </div>
      </div>
      <div class="col-md-4">
        <label for="categoryFilter" class="form-label">Filter by Category:</label>
        <select id="categoryFilter" class="form-control">
          <option value="">All</option>
          <option value="Incoming">Incoming</option>
          <option value="Outgoing">Outgoing</option>
          <!-- Add additional category options as needed -->
        </select>
      </div>
    </div>
    <div class="row mb-3" >
      <div class="col-md-4 ">
        <button id="exportExcel" class="btn btn-secondary">Export to Excel</button>
      </div>
    </div>
    <table class="table table-bordered" id="dataTable" >
        <thead>
            <tr>
                <th rowspan="2">Category</th>
                <th rowspan="2">Document Name</th>
                <th rowspan="2">From/Origin</th>
                <th rowspan="2">Purpose</th>
                <th colspan="4" class="text-center">Routing History</th>
                <th rowspan="2">Actions</th>
            </tr>
            <tr>
                <th>Br/Off/Unit</th>
                <th>Received By</th>
                <th>Date and Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($documents as $document)
@php
    $rowspan = max(1, $document->locations->count());
@endphp

@if ($document->locations->isEmpty())
    <tr data-document-id="{{ $document->id }}">
        <td rowspan="{{ $rowspan }}" class="document-detail" data-field="category">{{ $document->category }}</td>
        <td rowspan="{{ $rowspan }}" class="document-detail" data-field="name">{{ $document->name }}</td>
        <td rowspan="{{ $rowspan }}" class="document-detail" data-field="drafter">{{ $document->drafter }}</td>
        <td rowspan="{{ $rowspan }}" class="document-detail" data-field="purpose">{{ $document->purpose }}</td>
        <td colspan="4" class="text-center text-muted">No routing history available</td>
        <td rowspan="{{ $rowspan }}">
            <a href="{{ route('documents.show', $document->id) }}" class="btn btn-info btn-sm">View</a>
            <!-- NEW: Inline edit buttons for document details -->
            <button class="btn btn-warning btn-sm editDocumentBtn">Edit Document</button>
            <button class="btn btn-success btn-sm saveDocumentBtn d-none">Save Document</button>
            <button class="btn btn-secondary btn-sm cancelDocumentBtn d-none">Cancel</button>
            <!-- End NEW -->
            <button class="btn btn-success btn-sm addRoutingBtn" data-bs-toggle="modal" data-bs-target="#addRoutingModal-{{ $document->id }}">Add Routing</button>
            <form action="{{ route('documents.destroy', $document->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this document?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
        </td>
    </tr>
@else
    @foreach ($document->locations as $index => $location)
        <tr data-document-id="{{ $document->id }}" data-id="{{ $location->id }}">
            @if ($index === 0)
                <td rowspan="{{ $rowspan }}" class="document-detail" data-field="category">{{ $document->category }}</td>
                <td rowspan="{{ $rowspan }}" class="document-detail" data-field="name">{{ $document->name }}</td>
                <td rowspan="{{ $rowspan }}" class="document-detail" data-field="drafter">{{ $document->drafter }}</td>
                <td rowspan="{{ $rowspan }}" class="document-detail" data-field="purpose">{{ $document->purpose }}</td>
                <!-- Routing history cells for the first row -->
                <td class="editable routing-history" data-field="location">{{ $location->location }}</td>
                <td class="editable" data-field="receiver">{{ $location->receiver }}</td>
                <td class="editable" data-field="timestamp">{{ \Carbon\Carbon::parse($location->timestamp)->format('Y-m-d H:i') }}</td>
                <td>
                    <button class="btn btn-warning btn-sm editRoutingBtn">Edit</button>
                    <button class="btn btn-success btn-sm saveRoutingBtn d-none">Save</button>
                    <button class="btn btn-secondary btn-sm cancelRoutingBtn d-none">Cancel</button>
                    <button class="btn btn-danger btn-sm deleteRoutingBtn" data-id="{{ $location->id }}">Delete</button>
                </td>
                <td rowspan="{{ $rowspan }}">
                    <a href="{{ route('documents.show', $document->id) }}" class="btn btn-info btn-sm">View</a>
                    <!-- NEW: Inline edit buttons for document details -->
                    <button class="btn btn-warning btn-sm editDocumentBtn">Edit Document</button>
                    <button class="btn btn-success btn-sm saveDocumentBtn d-none">Save Document</button>
                    <button class="btn btn-secondary btn-sm cancelDocumentBtn d-none">Cancel</button>
                    <!-- End NEW -->
                    <button class="btn btn-success btn-sm addRoutingBtn" data-bs-toggle="modal" data-bs-target="#addRoutingModal-{{ $document->id }}">Add Routing</button>
                    <form action="{{ route('documents.destroy', $document->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this document?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            @else
                <td class="editable" data-field="location">{{ $location->location }}</td>
                <td class="editable" data-field="receiver">{{ $location->receiver }}</td>
                <td class="editable" data-field="timestamp">{{ \Carbon\Carbon::parse($location->timestamp)->format('Y-m-d H:i') }}</td>
                <td>
                    <button class="btn btn-warning btn-sm editRoutingBtn">Edit</button>
                    <button class="btn btn-success btn-sm saveRoutingBtn d-none">Save</button>
                    <button class="btn btn-secondary btn-sm cancelRoutingBtn d-none">Cancel</button>
                    <button class="btn btn-danger btn-sm deleteRoutingBtn" data-id="{{ $location->id }}">Delete</button>
                </td>
            @endif
        </tr>
    @endforeach
@endif


                <!-- Add Routing Modal -->
                <div class="modal fade" id="addRoutingModal-{{ $document->id }}" tabindex="-1" aria-labelledby="addRoutingModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('locations.store') }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Routing History</h5>
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
                                        <label>Date and Time</label>
                                        <input type="datetime-local" name="timestamp" class="form-control" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Add Routing</button>
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

<!-- Ensure Bootstrap JS is loaded -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
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
    let id = button.attr('data-id');
    let docId = row.attr('data-document-id');

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
                    routingCell.attr('colspan', 4)
           .addClass('text-center')
           .html('<span class="text-muted">No routing history available</span>');

                } else {
                    // More than one routing row exists.
                    // If the row being deleted is the document row (the first row), promote the next row.
                    let documentRow = docRows.first();
                    if ( row.is(documentRow) ) {
                        let newDocRow = docRows.eq(1);
                        // Copy the document cells (the first 4 cells) from the old document row.
                        let docInfoCells = documentRow.children('td').slice(0, 4).clone();
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
        let newRow = $("<tr>").html(`
            <td rowspan="${rowspan}" class="document-detail" data-field="category">${doc.category}</td>
            <td rowspan="${rowspan}" class="document-detail" data-field="name">${doc.name}</td>
            <td rowspan="${rowspan}" class="document-detail" data-field="drafter">${doc.drafter}</td>
            <td rowspan="${rowspan}" class="document-detail" data-field="purpose">${doc.purpose}</td>
            ${doc.locations.length > 0 ? `
                <td  class="editable" data-field="location">${doc.locations[0].location}</td>
                <td  class="editable" data-field="receiver">${doc.locations[0].receiver}</td>
                <td  class="editable" data-field="timestamp">${new Date(doc.locations[0].timestamp).toLocaleString()}</td>
                <td>
                    <button class="btn btn-warning btn-sm editRoutingBtn">Edit</button>
                    <button class="btn btn-success btn-sm saveRoutingBtn d-none">Save</button>
                    <button class="btn btn-secondary btn-sm cancelRoutingBtn d-none">Cancel</button>
                    <button class="btn btn-danger btn-sm deleteRoutingBtn" data-id="${doc.locations[0].id }">Delete</button>
                </td>
            ` : `<td colspan="4" class="text-center text-muted">No routing history available</td>`}
            <td rowspan="${rowspan}">
                <a href="/documents/${doc.id}" class="btn btn-info btn-sm">View</a>
                <button class="btn btn-warning btn-sm editDocumentBtn">Edit Document</button>
                <button class="btn btn-success btn-sm saveDocumentBtn d-none">Save Document</button>
                <button class="btn btn-secondary btn-sm cancelDocumentBtn d-none">Cancel</button>
                <button class="btn btn-success btn-sm addRoutingBtn" data-bs-toggle="modal" data-bs-target="#addRoutingModal-${doc.id}">Add Routing</button>
                <form action="/documents/${doc.id}" method="POST" class="delete-form" data-id="${doc.id}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this document?');">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${doc.id}">Delete</button>
                </form>
            </td>
        `);
        newRow.attr('data-document-id', `${doc.id}`);
        newRow.attr('data-id', `${doc.locations[0].id}`);

        tableBody.append(newRow);
        doc.locations.slice(1).forEach(function(location) {
            let historyRow = $("<tr>").html(`
                <td>${location.location}</td>
                <td>${location.receiver}</td>
                <td>${new Date(location.timestamp).toLocaleString()}</td>
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
                    $('#addDocumentForm')[0].reset();
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
                }
            },
            error: function (xhr) {
                alert("Error: " + xhr.responseJSON.message);
            }
        });
    });

    $('#addRoutingModal form').submit(function (e) {
        e.preventDefault();
        let form = $(this);
        let formData = new FormData(form[0]);
        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    alert("Routing history added successfully!");
                    $('#addRoutingModal').modal('hide');
                    location.reload();
                }
            },
            error: function (xhr) {
                alert("Error: " + xhr.responseJSON.message);
            }
        });
    });

    $("tbody").on("click", ".editRoutingBtn", function () {
        let row = $(this).closest("tr");
        row.find(".editable").each(function () {
            let text = $(this).text().trim();
            let field = $(this).data("field");
            if (field === "timestamp") {
                let dateTime = new Date(text).toISOString().slice(0, 16);
                $(this).html(`<input type="datetime-local" class="form-control" name="${field}" value="${dateTime}">`);
            } else {
                $(this).html(`<input type="text" class="form-control" name="${field}" value="${text}">`);
            }
        });
        row.find(".editRoutingBtn").addClass("d-none");
        row.find(".saveRoutingBtn, .cancelRoutingBtn").removeClass("d-none");
    });

    $("tbody").on("click", ".cancelRoutingBtn", function () {
        let row = $(this).closest("tr");
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
    });

    $("tbody").on("click", ".saveRoutingBtn", function () {
        let row = $(this).closest("tr");
        let id = row.data("id");
        let updatedData = {
            _token: "{{ csrf_token() }}",
            location: row.find("input[name='location']").val(),
            receiver: row.find("input[name='receiver']").val(),
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
        let dateCell = docRow.find('[data-field="timestamp"]');
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
        let category = docRow.find('td:first').text().trim().toLowerCase();
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
      }
      // For the second header row, remove the last cell (Routing History Actions)
      else if (index === 1) {
        $(this).find('th:last').remove();
      }
    });

    // Remove the unwanted "Actions" columns from the body rows.
    tableClone.find('tbody tr').each(function(){
      let $cells = $(this).children();
      // For rows with 9 cells (document rows)
      if ($cells.length >= 9) {
        $cells.eq(8).remove(); // Remove global actions
        $cells.eq(7).remove(); // Remove routing history actions (after removal, indexes shift)
      }
      // For subsequent routing rows that have 4 cells:
      else if ($cells.length === 4) {
        $cells.eq(3).remove(); // Remove routing actions cell
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
    
    // Iterate over each document detail cell
    docRow.find(".document-detail").each(function () {
        let currentText = $(this).text().trim();
        let field = $(this).data("field");
        
        if (field === "category") {
            // Define dropdown options for Category
            let options = `
                <option value="Incoming" ${currentText === "Incoming" ? "selected" : ""}>Incoming</option>
                <option value="Outgoing" ${currentText === "Outgoing" ? "selected" : ""}>Outgoing</option>
            `;
            $(this).html(`<select class="form-control" name="${field}">${options}</select>`);
        } else if (field === "purpose") {
            // Define dropdown options for Purpose
            let options = `
                <option value="For Signature" ${currentText === "For Signature" ? "selected" : ""}>For Signature</option>
                <option value="For Route" ${currentText === "For Route" ? "selected" : ""}>For Route</option>
                <option value="RWC" ${currentText === "RWC" ? "selected" : ""}>RWC</option>
                <option value="For Appropriate Action" ${currentText === "For Appropriate Action" ? "selected" : ""}>For Appropriate Action</option>
                <option value="For Info" ${currentText === "For Info" ? "selected" : ""}>For Info</option>
                <option value="For Reference" ${currentText === "For Reference" ? "selected" : ""}>For Reference</option>
                <option value="For Concurrence" ${currentText === "For Concurrence" ? "selected" : ""}>For Concurrence</option>
                <option value="Others" ${currentText === "Others" ? "selected" : ""}>Others</option>
            `;
            $(this).html(`<select class="form-control" name="${field}">${options}</select>`);
        } else {
            // For other fields (e.g., name, drafter), use a text input.
            $(this).html(`<input type="text" class="form-control" name="${field}" value="${currentText}">`);
        }
    });
    
    // Toggle buttons: hide Edit, show Save/Cancel
    docRow.find(".editDocumentBtn").addClass("d-none");
    docRow.find(".saveDocumentBtn, .cancelDocumentBtn").removeClass("d-none");
});

$("tbody").on("click", ".cancelDocumentBtn", function () {
    let docRow = $(this).closest("tr");
    // For each document detail cell, revert the input/select field back to its original text.
    docRow.find(".document-detail").each(function () {
        // Retrieve the original value from the input's or select's "value" attribute or selected option.
        let originalValue = $(this).find("input, select").is("select")
            ? $(this).find("select option:selected").text()
            : $(this).find("input").attr("value");
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
                    // For dropdowns, get the selected text.
                    let newValue = (field === "category" || field === "purpose")
                        ? $(this).find("select option:selected").text()
                        : updatedData[field];
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
