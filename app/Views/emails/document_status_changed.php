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
        .status-box { background-color: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0; text-align: center; }
        .status-box .status { font-size: 20px; font-weight: bold; color: #0056b3; }
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
            <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Sistema de Requerimientos</p>
        </div>
        <div class="content">
            <p>Hola <strong><?= esc($client['first_name'] . ' ' . $client['last_name']) ?></strong>,</p>
            <p>Te informamos que tu requerimiento <strong><?= esc($document['document_code'] ?? "#" . $document['id']) ?> - <?= esc($document['title']) ?></strong> ha cambiado de estado.</p>
            
            <div class="status-box">
                <p style="margin: 0; color: #666;">Nuevo Estado:</p>
                <div class="status"><?= esc($newStatus) ?></div>
                <?php if ($oldStatus !== 'Sin estado'): ?>
                    <p style="margin: 5px 0 0 0; font-size: 14px; color: #888;">(Anteriormente: <?= esc($oldStatus) ?>)</p>
                <?php endif; ?>
            </div>

            <p>Puedes consultar el progreso, historial completo y descargar documentos asociados accediendo a nuestra plataforma pública utilizando tu número de cédula (<?= esc($client['cedula']) ?>).</p>
            
            <div style="text-align: center;">
                <a href="<?= base_url('consulta-requerimientos?cedula=' . $client['cedula']) ?>" class="btn">Consultar Mi Requerimiento</a>
            </div>
        </div>
        <div class="footer">
            <p>Este es un mensaje automático generado por el Sistema de Requerimientos de CNEL. Por favor, no respondas a este correo.</p>
            <p>&copy; <?= date('Y') ?> CNEL EP. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>