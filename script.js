// Dark Mode Toggle
  const toggleButton = document.getElementById("toggle-dark-mode");
  toggleButton.addEventListener("click", () => {
    document.body.classList.toggle("dark-mode");
    toggleButton.textContent = document.body.classList.contains("dark-mode") 
      ? "☀️ Light Mode" 
      : "🌙 Dark Mode";
  });

  // File Name Display
  const fileInput = document.getElementById("fileInput");
  const fileNameDisplay = document.getElementById("fileNameDisplay");

  fileInput.addEventListener("change", function () {
    if (fileInput.files.length > 0) {
      fileNameDisplay.textContent = `📄 Selected: ${fileInput.files[0].name}`;
    } else {
      fileNameDisplay.textContent = "";
    }
  });
  
  
document.querySelectorAll('form').forEach(form => {
  form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitButton = this.querySelector('input[type="submit"]');
    const originalButtonText = submitButton.value;

    try {
      submitButton.value = 'Converting...';
      submitButton.disabled = true;

      const response = await fetch(this.action, {
        method: 'POST',
        body: formData
      });

      const text = await response.text();
      let result;

      try {
        result = JSON.parse(text);
      } catch (jsonError) {
        console.error("❌ JSON Parse Error:", text);
        throw new Error("Server returned an invalid response. Enable PHP error logging to see more.");
      }

      if (result.error) {
        alert(result.error);
      } else if (result.success) {
        const downloadLink = document.createElement('a');
        downloadLink.href = result.downloadUrl;
        downloadLink.download = result.filename;
        downloadLink.style.display = 'none';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);

        alert('✅ Conversion complete! Your file is downloading.');
      }
    } catch (error) {
      console.error('💥 Conversion Error:', error);
      alert('Error: ' + error.message);
    } finally {
      submitButton.value = originalButtonText;
      submitButton.disabled = false;
    }
  });
});