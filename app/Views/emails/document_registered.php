<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { border-bottom: 2px solid #0056b3; padding-bottom: 10px; margin-bottom: 20px; text-align: center; }
        .header h1 { color: #0056b3; margin: 0; font-size: 24px; }
        .content p { font-size: 16px; color: #333333; line-height: 1.5; }
        .info-box { background-color: #f8f9fa; border-left: 4px solid #0056b3; padding: 15px; margin: 20px 0; }
        .footer { margin-top: 30px; font-size: 12px; color: #777777; text-align: center; border-top: 1px solid #eeeeee; padding-top: 10px; }
        .btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #fff;
    border: 2px solid #0056b3; /* 👈 borde correcto */
    border-radius: 5px; /* 👈 esquinas redondeadas */
    color: #0056b3;
    text-decoration: none;
    margin-top: 20px;
    font-weight: bold;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CNEL EP</h1>
            <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Confirmación de Registro</p>
        </div>
        <div class="content">
            <p>Hola <strong><?= esc($client['first_name'] . ' ' . $client['last_name']) ?></strong>,</p>
            <p>Tu requerimiento ha sido ingresado exitosamente en nuestro sistema.</p>
            
            <div class="info-box">
                <p style="margin: 0;"><strong>Código de Trámite:</strong> <?= esc($document['document_code'] ?? "#" . $document['id']) ?></p>
                <p style="margin: 5px 0 0 0;"><strong>Título:</strong> <?= esc($document['title']) ?></p>
                <p style="margin: 5px 0 0 0;"><strong>Estado Inicial:</strong> Recibido / Pendiente</p>
            </div>

            <p>Estaremos notificándote vía correo electrónico sobre cada avance significativo en el procesamiento de tu solicitud.</p>
            
            <p>Puedes realizar el seguimiento en tiempo real a través de nuestra plataforma:</p>
            
            <div style="text-align: center;">
                <a href="<?= base_url('consulta-requerimientos?cedula=' . $client['cedula']) ?>" class="btn">Consultar Mi Trámite</a>
            </div>
        </div>
        <div class="footer">
            <p>Este es un mensaje automático generado por el Sistema de Requerimientos de CNEL. Por favor, no respondas a este correo.</p>
            <p>&copy; <?= date('Y') ?> CNEL EP. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>