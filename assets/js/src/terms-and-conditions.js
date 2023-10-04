document.getElementById("termsPdfDownloadButton").addEventListener("click", function () {
    // Redirect to the Symfony route that triggers the download
    // window.location.href = "{{ path('download_pdf') }}";
    window.location.href = "/download-pdf";
});