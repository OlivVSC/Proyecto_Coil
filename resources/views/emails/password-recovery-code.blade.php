
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>MarsExplorer | Recuperar contraseña</title>

    <style>
        @media only screen and (max-width: 600px) {
            .email-card {
                width: 100% !important;
            }

            .email-body {
                padding: 26px 18px !important;
            }

            .email-logo {
                width: 230px !important;
                max-width: 90% !important;
            }

            .verification-code {
                font-size: 32px !important;
                letter-spacing: 5px !important;
            }
        }
    </style>
</head>

<body style="
    margin:0;
    padding:0;
    background-color:#eef1f6;
    font-family:Arial,Helvetica,sans-serif;
">

    <div style="
        display:none;
        max-height:0;
        overflow:hidden;
        opacity:0;
    ">
        Tu código de verificación de MarsExplorer.
        Válido durante 10 minutos.
    </div>

    <table
        role="presentation"
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
        style="background-color:#eef1f6;"
    >
        <tr>
            <td align="center" style="padding:30px 12px;">

                <table
                    role="presentation"
                    class="email-card"
                    width="560"
                    cellpadding="0"
                    cellspacing="0"
                    border="0"
                    style="
                        width:560px;
                        max-width:100%;
                        background-color:#ffffff;
                        border:1px solid #dfe5ee;
                        border-radius:18px;
                        overflow:hidden;
                    "
                >

                    <!-- LOGO DEL PROYECTO -->
                    <tr>
                        <td align="center" style="
                            background-color:#111315;
                            padding:24px 20px;
                            border-bottom:5px solid #f47725;
                        ">

                            <img
                                class="email-logo"
                                src="{{ $message->embed(public_path('img/logo-cohete-marsexplorer.png')) }}"
                                alt="MarsExplorer"
                                width="270"
                                style="
                                    display:block;
                                    width:270px;
                                    max-width:100%;
                                    height:auto;
                                    margin:0 auto;
                                    border:0;
                                "
                            >

                        </td>
                    </tr>

                    <!-- CONTENIDO -->
                    <tr>
                        <td class="email-body" style="
                            padding:38px 32px 32px;
                            color:#14243d;
                        ">

                            <h1 style="
                                margin:0 0 18px;
                                color:#132540;
                                font-size:27px;
                                line-height:1.3;
                                text-align:center;
                            ">
                                Recupera tu contraseña
                            </h1>

                            <p style="
                                margin:0 0 26px;
                                color:#4a607c;
                                text-align:center;
                                font-size:15px;
                                line-height:1.8;
                            ">
                                Hemos recibido una solicitud
                                para recuperar el acceso a tu
                                cuenta de
                                <strong style="color:#ef762b;">
                                    MarsExplorer
                                </strong>.
                                Utiliza el siguiente código
                                para continuar.
                            </p>

                            <!-- CODIGO -->
                            <table
                                role="presentation"
                                width="100%"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                style="
                                    background-color:#10213c;
                                    border:2px solid #f47725;
                                    border-radius:15px;
                                "
                            >
                                <tr>
                                    <td align="center" style="
                                        padding:28px 12px;
                                    ">

                                        <p style="
                                            margin:0 0 15px;
                                            color:#e1eaf5;
                                            font-size:13px;
                                            font-weight:bold;
                                        ">
                                            TU CÓDIGO DE VERIFICACIÓN
                                        </p>

                                        <p
                                            class="verification-code"
                                            style="
                                                margin:0;
                                                color:#ffad75;
                                                font-size:40px;
                                                font-weight:bold;
                                                letter-spacing:8px;
                                                line-height:1.4;
                                            "
                                        >{{ $code }}</p>

                                    </td>
                                </tr>
                            </table>

                            <!-- EXPIRACION -->
                            <p style="
                                margin:23px 0 27px;
                                text-align:center;
                                color:#405674;
                                font-size:14px;
                                line-height:1.7;
                            ">
                                Este código expirará en
                                <strong style="color:#ed742c;">
                                    10 minutos
                                </strong>.
                            </p>

                            <!-- SEGURIDAD -->
                            <table
                                role="presentation"
                                width="100%"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                style="
                                    background-color:#fff2e9;
                                    border:1px solid #ffe0c9;
                                    border-radius:12px;
                                "
                            >
                                <tr>
                                    <td style="padding:18px 20px;">

                                        <p style="
                                            margin:0 0 8px;
                                            color:#14243d;
                                            font-size:15px;
                                            font-weight:bold;
                                        ">
                                            Tu seguridad es importante
                                        </p>

                                        <p style="
                                            margin:0;
                                            color:#405674;
                                            font-size:13px;
                                            line-height:1.7;
                                        ">
                                            Nunca compartas este código.
                                            Si no solicitaste recuperar
                                            tu contraseña, puedes
                                            ignorar este mensaje.
                                        </p>

                                    </td>
                                </tr>
                            </table>

                            <p style="
                                margin:28px 0 8px;
                                text-align:center;
                                color:#405674;
                                font-size:14px;
                            ">
                                Gracias por formar parte
                                de nuestra misión.
                            </p>

                            <p style="
                                margin:0;
                                text-align:center;
                                color:#ed742c;
                                font-size:15px;
                                font-weight:bold;
                            ">
                                Equipo MarsExplorer
                            </p>

                        </td>
                    </tr>

                    <!-- PIE -->
                    <tr>
                        <td align="center" style="
                            padding:24px 20px;
                            background-color:#10213c;
                            border-top:4px solid #f47725;
                        ">

                            <p style="
                                margin:0 0 8px;
                                color:#ffffff;
                                font-size:17px;
                                font-weight:bold;
                            ">
                                Mars<span style="color:#ff8738;">Explorer</span>
                            </p>

                            <p style="
                                margin:0 0 12px;
                                color:#c1d0e2;
                                font-size:12px;
                            ">
                                Explora · Aprende · Descubre
                            </p>

                            <p style="
                                margin:0;
                                color:#91a6c0;
                                font-size:11px;
                                line-height:1.7;
                            ">
                                Este mensaje se generó automáticamente.
                                No es necesario responder.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
