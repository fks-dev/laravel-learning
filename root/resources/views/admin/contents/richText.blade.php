<!-- Quill CDN -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
    const quill = new Quill('#editor', {
        theme: 'snow'
    });

    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        let content = quill.root.innerHTML;
        document.getElementById('text').value = content;
    })
</script>