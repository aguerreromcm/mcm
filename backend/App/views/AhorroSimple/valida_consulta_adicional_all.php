<?php echo $header; ?>

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
        box-shadow: 0 0px 0px rgba(0, 0, 0, 0); /* sombra suave para efecto flotante */
        color: #333;
        transition: box-shadow 0.4s ease;
        
    }

    /* Animación levitar */
    @keyframes levitar {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-4px); } /* muy sutil */
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
            text-shadow: 0 0 10px rgb(119, 12, 40);
        }
        to {
            text-shadow: 0 0 18px rgba(140, 140, 140, 0.6);
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
        max-width: 700px;239, 9, 12, 0.4
        transition: all 0.3s ease;
        border: 1px solid rgba(36, 39, 208, 0.1);
        box-shadow: 0 6px 20px rgba(9, 9, 9, 0.76);
        margin: 20px auto 0 auto; /* centrado horizontal */
    }

    .buscador-box:hover {
        transform: scale(1.02);
        box-shadow: 0 0 25px rgba(239, 9, 12, 0.4);
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
        background: linear-gradient(45deg, #770c28, #932e49);
        color: white;
        padding: 16px 35px;
        font-size: 1.3rem;
        border-radius: 50px;
        cursor: pointer;
        transition: 0.3s ease;
    }

    .buscador-box button:hover {
        background: linear-gradient(45deg, #770c28, #de7b9b);
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

<div class="right_col" role="main">
    <div class="estado-cuenta-wrapper">
        <h1>Valida si el crédito adicional es viable</h1>
        <p>Introduce el <strong>código del crédito</strong> de la cuente eje.</p>

        <form action="/AhorroSimple/ValidaAdicional/" method="GET" class="buscador-box">
            <input type="text" id="cdgns" name="cdgns" placeholder="Ejemplo: 006592" autofocus required value="<?php echo $CDGNS; ?>">
            <button type="submit"><i class="fa fa-search"></i> Buscar</button>
        </form>
    </div>
</div>

<?php echo $footer; ?>
