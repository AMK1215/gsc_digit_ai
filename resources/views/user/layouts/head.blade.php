<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MoneyKing</title>
    <link rel="icon" href="{{ asset('/assets/img/logo-mk.png') }}">
    <link rel="stylesheet" href="{{ asset('slot_app/css/style.css') }}" />
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Alumni+Sans+Inline+One&family=Inter&family=Poppins:wght@300;400;500&family=Rubik+Mono+One&display=swap"
        rel="stylesheet" />

    <!-- Material Css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/material-icons@1.13.12/iconfont/material-icons.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- font awesome  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @yield('style')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');

        * {
            /* font-family: 'Poppins', sans-serif; */
            font-family: 'Ubuntu', sans-serif;
        }

        #main {
            background-image: url("{{ asset('assets/img/background.webp') }}");
            /* Dynamically set background image */
            /* fallback background color, uncomment if needed */
            /* background: #000; */
            min-height: 100vh;
            /* Ensure the container takes up the full viewport height */
            background-repeat: no-repeat;
            /* Prevent the image from repeating */
            background-size: cover;
            /* Ensure the image covers the entire container */
            background-position: center;
            /* Center the image within the container */
        }

        .login-card {
            box-shadow: 1px 2px 3px rgb(255, 0, 0);
            background-color: rgba(119, 0, 0, 0.5);
        }
    </style>
</head>
