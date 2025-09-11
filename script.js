function saveNote() {
    const title = document.getElementById("noteTitle").value;
    const content = document.getElementById("noteContent").value;

    if (!title || !content) {
        alert("Please fill in both fields.");
        return;
    }

    const formData = new FormData();
    formData.append("title", title);
    formData.append("content", content);

    fetch("save_note.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if (data.includes("success")) {
            alert("Note added successfully!");
            location.reload();
        } else {
            alert("Error: " + data);
        }
    })
    .catch(err => alert("Fetch error: " + err));
}