<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script> <!-- XLSX for Excel -->
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            font-family: 'Inter', sans-serif;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
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
        .btn-primary, .btn-outline-primary, .btn-success {
            border-radius: 2rem;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }
        .btn-primary:hover, .btn-outline-primary:hover, .btn-success:hover {
            transform: scale(1.05);
        }
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.2rem;
            }
            .btn-primary, .btn-outline-primary, .btn-success {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
            .list-group-item {
                padding: 10px;
                font-size: 0.9rem;
                flex-direction: column;
                align-items: flex-start;
            }
            .list-group-item a {
                margin-bottom: 0.5rem;
            }
        }
        @media (max-width: 576px) {
            .container {
                padding: 0 15px;
            }
            .btn-primary, .btn-outline-primary, .btn-success {
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
            }
            .list-group-item {
                padding: 8px;
                font-size: 0.8rem;
                flex-direction: column;
                align-items: flex-start;
            }
            .list-group-item a {
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>


    <div class="container mt-4">
        <h2 class="text-center fw-bold">
            <small class="text-muted d-block">🔍 Found: {{ $totalCount }}</small>
        </h2>

        @if (count($results) > 0)
            <div class="d-flex justify-content-end">
                <button class="btn btn-success mb-2" onclick="exportToExcel()">📥 Export to Excel</button>
            </div>
            <div class="list-group mt-3">
                @foreach ($results as $result)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{ $result }}" target="_blank" class="copy-link" data-url="{{ $result }}">{{ $result }}</a>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 p-3">
                @if ($page > 1)
                    <a href="{{ url('/search') }}?keywords={{ $keywords }}&location={{ $location }}&platform={{ $platform }}&page={{ $page - 1 }}" class="btn btn-outline-primary">⬅️ Previous</a>
                @else
                    <div></div>
                @endif
                    <a href="{{ url('/') }}" class="btn btn-primary">🔄 Start Over</a>

                @if (count($results) == 10)
                    <a href="{{ url('/search') }}?keywords={{ $keywords }}&location={{ $location }}&platform={{ $platform }}&page={{ $page + 1 }}" class="btn btn-outline-primary">Next ➡️</a>
                @else
                    <div></div>
                @endif
            </div>
        @else
            <div class="alert alert-warning text-center mt-3" role="alert">
                Oops! No results found. 🕵️‍♂️ Try a different keyword!
            </div>
        @endif

    </div>


    <script>
        // Copy link functionality for mobile devices
        document.querySelectorAll('.copy-link').forEach(link => {
            let touchDuration;
            link.addEventListener('touchstart', function(e) {
                touchDuration = setTimeout(() => {
                    let url = this.getAttribute('data-url');
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(url).then(() => {
                            alert("✅ Link copied to clipboard!");
                        }).catch(err => console.error('Copy failed', err));
                    } else {
                        let textarea = document.createElement('textarea');
                        textarea.value = url;
                        document.body.appendChild(textarea);
                        textarea.select();
                        try {
                            document.execCommand('copy');
                            alert("✅ Link copied to clipboard!");
                        } catch (err) {
                            console.error('Copy failed', err);
                        }
                        document.body.removeChild(textarea);
                    }
                }, 500); // 500ms for long press
            });

            link.addEventListener('touchend', function(e) {
                if (touchDuration) {
                    clearTimeout(touchDuration);
                }
            });

            link.addEventListener('click', function(e) {
                if (touchDuration) {
                    clearTimeout(touchDuration);
                }
            });
        });

        // Export results to Excel
        function exportToExcel() {
            let data = [['Results']];
            document.querySelectorAll('.list-group-item a').forEach(link => {
                data.push([link.href]);
            });

            let ws = XLSX.utils.aoa_to_sheet(data);
            let wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Search Results");
            XLSX.writeFile(wb, "search_results.xlsx");
        }
    </script>

</body>
</html>
