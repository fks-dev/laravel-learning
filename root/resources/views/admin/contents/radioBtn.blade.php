<script>
    document.addEventListener("DOMContentLoaded", (event) => {
        showText();
    });

    //　表示テキスト
    const rich = document.getElementById('rich');
    const movie = document.getElementById('movie');
    const url = document.getElementById('url');
    const file = document.getElementById('file');
    const test = document.getElementById('test');
    const remarks = document.getElementById('remarks');

    function showText() {
        // ボタン
        const option1 = document.getElementById('ContentLabel');
        const option2 = document.getElementById('ContentHTML');
        const option3 = document.getElementById('ContentMovie');
        const option4 = document.getElementById('ContentURL');
        const option5 = document.getElementById('ContentFile');
        const option6 = document.getElementById('ContentTest');

        if (option1.checked) {
            downText();
        } else if (option2.checked) {
            downText();
            rich.style.display = "";
            remarks.style.display = "";
        } else if (option3.checked) {
            downText();
            movie.style.display = "";
            remarks.style.display = "";
        } else if (option4.checked) {
            downText();
            url.style.display = ""
            remarks.style.display = "";
        } else if (option5.checked) {
            downText();
            file.style.display = ""
            remarks.style.display = "";
        } else if (option6.checked) {
            downText();
            test.style.display = ""
            remarks.style.display = "";
        }
    }

    function downText() {

        rich.style.display = "none";
        movie.style.display = "none";
        url.style.display = "none";
        file.style.display = "none";
        test.style.display = "none";
        remarks.style.display = "none";
    }


</script>
