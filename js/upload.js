document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("project-upload-form");
    const thumbnailInput = document.getElementById("project-thumbnail");
    const demoVideoInput = document.getElementById("demo-video");
    const agreementCheckbox = document.getElementById("agreement");

    // 👁 Preview selected image (optional)
    thumbnailInput.addEventListener("change", function () {
        const file = thumbnailInput.files[0];
        if (file && file.size > 5 * 1024 * 1024) {
            alert("Project thumbnail must be less than 5MB.");
            thumbnailInput.value = "";
        }
    });

    // 🎥 Validate demo video file size (max 100MB)
    demoVideoInput.addEventListener("change", function () {
        const file = demoVideoInput.files[0];
        if (file && file.size > 100 * 1024 * 1024) {
            alert("Demo video must be less than 100MB.");
            demoVideoInput.value = "";
        }
    });

    // ✅ Validate form on submit
    form.addEventListener("submit", function (e) {
        if (!agreementCheckbox.checked) {
            alert("You must confirm your agreement to proceed.");
            e.preventDefault();
        }

        // Add more checks if needed
    });
});
