<!-- JavaScript Bundle with Popper ver.5.2.3 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

<script>
let draggedRow = null;

function handleDragStart(event) {
    draggedRow = event.target;
    event.dataTransfer.effectAllowed = 'move';
}

function handleDragOver(event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';

    const targetRow = event.target.closest('tr');
    if (targetRow && targetRow !== draggedRow) {
        const tbody = targetRow.parentNode;
        const rect = targetRow.getBoundingClientRect();
        const next = (event.clientY - rect.top) > (rect.bottom - event.clientY);

        if (next) {
            tbody.insertBefore(draggedRow, targetRow.nextSibling);
        } else {
            tbody.insertBefore(draggedRow, targetRow);
        }
    }
}

function handleDragEnd() {
    draggedRow = null;
}

// 非同期処理
async function saveSortOrder() {
    const rows = document.querySelectorAll("#sortable tbody tr");
    const positions = Array.from(rows).map(row => row.dataset.id);

    try {
        const response = await fetch("{{ route('course.sort') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ positions: positions })
        });

        const data = await response.json();
        console.log(data.success);
    } catch (error) {
        console.error(error);
    }
}


function addDragHandlers() {
    const rows = document.querySelectorAll("#sortable tbody tr");
    rows.forEach(row => {
        row.addEventListener('dragstart', handleDragStart);
        row.addEventListener('dragover', handleDragOver);
        row.addEventListener('dragend', handleDragEnd);
        row.setAttribute('draggable', true);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    addDragHandlers();
    document.querySelector("#sortable tbody").addEventListener('dragend', saveSortOrder);
});</script>