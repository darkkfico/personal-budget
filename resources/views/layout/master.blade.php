<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/75475ebc14.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../output.css" type="text/css" />
    <link rel="icon" href="../images/favicon.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <title>@yield('title') - Budgetly</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html {
            scroll-behavior: smooth;
        }

        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        .updateBtn {
            visibility: hidden;

        }

        .updateInput {
            transform: translateX(80px);
        }

        .updateInput:focus {
            transform: translateX(0px);
        }

        .updateBtn:focus,
        .updateBtn:active {
            visibility: visible;
        }

        .updateInput:focus+.updateBtn {
            visibility: visible;
        }

        .items-panel {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .items-panel.open {
            max-height: 500px;
        }

        .category-arrow {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .category-arrow.open {
            transform: rotate(180deg);
        }

        @media (prefers-reduced-motion: reduce) {

            .items-panel,
            .category-arrow {
                transition: none;
            }
        }
    </style>
</head>

<body class="bg-linear-to-br from-secondary to-butter bg-fixed overflow-x-hidden min-w-0">

    @yield('header')

    @yield('content')


    @yield('footer')

</body>

</html>
