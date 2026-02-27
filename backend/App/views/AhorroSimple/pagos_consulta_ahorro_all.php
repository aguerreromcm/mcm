<?= $header; ?>

<div class="right_col" role="main">
    <div class="estado-cuenta-wrapper">
        <h1>Consulta Estados de Cuenta Ahorro</h1>
        <p>Introduce el <strong>código del crédito</strong> para ver su estado de cuenta.</p>

        <div class="buscador-box">
            <input type="text" id="credito" name="credito" placeholder="Ejemplo: 006592" maxlength="6" value="<?= $_GET['credito'] ?>" autofocus required>
            <button type="button"><i class="fa fa-search"></i> Buscar</button>
        </div>
    </div>
    <?php
    if (isset($_GET['credito'])) {
        echo <<<HTML
        <div class="row" style="display: flex; justify-content: center;">
            <div class="col-md-6">
                <div class="alert alert-warning alert-dismissable">
                    <button type="button" class="close" style="color: black;" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <label style="font-size: 14px; color: black;">El crédito {$_GET['credito']} no tiene contrato de ahorro.</label>
                    <ul style="margin-left: 10px;">
                        <li style="color: black;">Valide que el número de crédito sea correcto.</li>
                        <li style="color: black;">Si el problema persiste, comuníquese con soporte técnico.</li>
                    </ul>
                </div>
            </div>
        </div>
        HTML;
    }
    ?>
</div>


<?= $footer; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputCredito = document.getElementById('credito');
        const botonBuscar = document.querySelector('.buscador-box button');

        function realizarBusqueda() {
            const credito = inputCredito.value.trim();
            if (credito.length === 6) {
                window.location.href = `/AhorroSimple/EstadoCuenta/?credito=${credito}`;
            } else {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissable';
                alertDiv.innerHTML = `
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>Error:</strong> El código del crédito debe tener 6 dígitos.
                `;
                alertDiv.style.position = 'fixed';
                alertDiv.style.bottom = '20px';
                alertDiv.style.right = '20px';
                document.querySelector('.right_col').appendChild(alertDiv);
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            }
        }

        botonBuscar.addEventListener('click', realizarBusqueda);

        inputCredito.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                realizarBusqueda();
            }
        });

        inputCredito.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
</script>

<style>
    /* === CONTENEDOR PRINCIPAL === */
    .estado-cuenta-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 80vh;
        text-align: center;
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(12px);
        border-radius: 20px;
        padding: 60px 30px;
        box-shadow: 0 0px 0px rgba(0, 0, 0, 0);
        /* sombra suave para efecto flotante */
        color: #333;
        transition: box-shadow 0.4s ease;

    }

    /* Animación levitar */
    @keyframes levitar {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-4px);
        }

        /* muy sutil */
    }

    .estado-cuenta-wrapper h1 {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #1e293b;
        text-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
        animation: glowPulse 2.5s infinite alternate;
    }

    @keyframes glowPulse {
        from {
            text-shadow: 0 0 10px rgba(0, 136, 255, 0.3);
        }

        to {
            text-shadow: 0 0 18px rgba(0, 204, 255, 0.6);
        }
    }

    /* === BUSCADOR === */
    .buscador-box {
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 60px;
        padding: 18px 28px;
        width: 100%;
        max-width: 700px;
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        margin: 20px auto 0 auto;
        /* centrado horizontal */
    }

    .buscador-box:hover {
        transform: scale(1.02);
        box-shadow: 0 0 25px rgba(0, 128, 255, 0.25);
    }

    .buscador-box input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        color: #333;
        font-size: 1.8rem;
        padding: 14px 20px;
    }

    .buscador-box input::placeholder {
        color: #777;
    }

    .buscador-box button {
        border: none;
        background: linear-gradient(45deg, #007bff, #00c6ff);
        color: white;
        padding: 16px 35px;
        font-size: 1.3rem;
        border-radius: 50px;
        cursor: pointer;
        transition: 0.3s ease;
    }

    .buscador-box button:hover {
        background: linear-gradient(45deg, #00c6ff, #007bff);
        transform: scale(1.08);
    }

    /* === RESPONSIVO === */
    @media (max-width: 768px) {
        .estado-cuenta-wrapper {
            padding: 40px 20px;
        }

        .buscador-box {
            max-width: 90%;
            padding: 14px 22px;
        }

        .buscador-box input {
            font-size: 1.4rem;
        }

        .buscador-box button {
            font-size: 1.1rem;
            padding: 12px 28px;
        }
    }
</style>