<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Media Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            font-family: 'Inter', sans-serif;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .form-control,
        .form-select {
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0279A8;
            box-shadow: 0 0 5px rgba(2, 121, 168, 0.5);
        }

        .btn-primary,
        .btn-secondary {
            border-radius: 2rem;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover,
        .btn-secondary:hover {
            transform: scale(1.05);
        }

        .list-group-item {
            border: none;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .list-group-item:hover {
            background-color: #eaf6ff;
            transform: scale(1.02);
        }

        .list-group-item.active {
            background-color: #0279A8;
            color: #fff;
        }

        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.2rem;
            }

            .btn-primary,
            .btn-secondary {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .list-group-item {
                padding: 10px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 0 15px;
            }

            .btn-primary,
            .btn-secondary {
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
            }

            .list-group-item {
                padding: 8px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container d-flex justify-content-center">
            <a class="navbar-brand" href="#">🔍 Social Media Detective</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card shadow-lg p-4 text-center">
            <h2 class="fw-bold">Find Your Next Follow! 🚀</h2>
            <p class="text-muted">Search for influencers, professionals, or long-lost friends!</p>

            <div class="col-md-12">
                <label class="form-label fw-semibold">API Key:</label>
                <a href="https://serpapi.com/manage-api-key" target="_blank">Get your API key</a>
                <input type="text" id="apiKeyInput" class="form-control" placeholder="Enter your API key">
                <button type="button" id="saveApiKeyButton" class="btn btn-secondary mt-2">Save API Key</button>
            </div>

            <form action="{{ url('/search') }}" method="GET" class="mt-4">
                <input type="hidden" name="apiKey" id="hiddenApiKeyInput">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Enter Keywords:</label>
                        <input type="text" name="keywords" id="keywordInput" class="form-control" required
                            placeholder="e.g. artist, developer, reading" autocomplete="off">
                        <ul id="suggestions" class="list-group position-absolute w-100"></ul>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Location:</label>
                        <input type="text" name="location" class="form-control" placeholder="e.g. Alexandria, Egypt">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Platform:</label>
                        <select name="platform" class="form-select" required>
                            <option value="facebook">Facebook</option>
                            <option value="instagram">Instagram</option>
                            <option value="linkedin">LinkedIn</option>
                            <option value="twitter">Twitter</option>
                            <option value="tiktok">TikTok</option>
                        </select>
                    </div>
                    <div class="col-md-12 text-center mt-3">
                        <button type="submit" class="btn btn-primary btn-lg w-100">🚀 Start Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <footer class="text-center mt-5">
        <p>Developed by <a href="https://wa.me/201010112468" target="_blank">Mazen Tamer</a></p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const apiKeyInput = document.getElementById("apiKeyInput");
            const saveApiKeyButton = document.getElementById("saveApiKeyButton");
            const hiddenApiKeyInput = document.getElementById("hiddenApiKeyInput");
            const form = document.querySelector("form");

            const savedApiKey = localStorage.getItem("apiKey");
            if (savedApiKey) {
                apiKeyInput.value = savedApiKey;
                hiddenApiKeyInput.value = savedApiKey;
            }

            saveApiKeyButton.addEventListener("click", function() {
                const apiKey = apiKeyInput.value.trim();
                if (apiKey) {
                    localStorage.setItem("apiKey", apiKey);
                    hiddenApiKeyInput.value = apiKey;
                    alert("API key saved successfully!");
                } else {
                    alert("Please enter a valid API key.");
                }
            });

            form.addEventListener("submit", function() {
                hiddenApiKeyInput.value = apiKeyInput.value.trim();
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("keywordInput");
            const list = document.getElementById("suggestions");
            let selectedIndex = -1; // Track keyboard selection

            input.addEventListener("input", function(e) {
                let query = input.value.trim().split(",").pop().trim(); // Get last term
                if (query.length < 2) {
                    list.innerHTML = '';
                    return;
                }

                fetch(`/autocomplete?query=${query}`)
                    .then(response => response.json())
                    .then(suggestions => {
                        list.innerHTML = '';
                        selectedIndex = -1; // Reset selection

                        suggestions.forEach((s, index) => {
                            let item = document.createElement("li");
                            item.className = "list-group-item";
                            item.textContent = s;
                            item.setAttribute("data-index", index);

                            item.onclick = () => insertSuggestion(s);
                            list.appendChild(item);
                        });
                    });
            });

            input.addEventListener("keydown", function(e) {
                let items = list.getElementsByTagName("li");

                if (e.key === "ArrowDown") {
                    e.preventDefault();
                    selectedIndex = (selectedIndex + 1) % items.length;
                    updateSelection(items);
                } else if (e.key === "ArrowUp") {
                    e.preventDefault();
                    selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                    updateSelection(items);
                } else if (e.key === "Enter" && selectedIndex !== -1) {
                    e.preventDefault();
                    insertSuggestion(items[selectedIndex].textContent);
                }
            });

            function insertSuggestion(selectedText) {
                let terms = input.value.split(",");
                terms[terms.length - 1] = " " + selectedText; // Replace only last term
                input.value = terms.join(",") + ", "; // Add a comma and space after
                list.innerHTML = ""; // Clear suggestions
                input.focus();
            }

            function updateSelection(items) {
                Array.from(items).forEach(item => item.classList.remove("active"));
                if (items[selectedIndex]) {
                    items[selectedIndex].classList.add("active");
                }
            }
        });
    </script>
</body>

</html>
