<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { border-bottom: 2px solid #28a745; padding-bottom: 10px; margin-bottom: 20px; text-align: center; }
        .header h1 { color: #28a745; margin: 0; font-size: 24px; }
        .content p { font-size: 16px; color: #333333; line-height: 1.5; }
        .info-box { background-color: #f8f9fa; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; }
        .instructions { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
        .footer { margin-top: 30px; font-size: 12px; color: #777777; text-align: center; border-top: 1px solid #eeeeee; padding-top: 10px; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            border: 2px solid #28a745;
            border-radius: 5px;
            color: #ffffff;
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
            <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Nuevo Requerimiento Asignado</p>
        </div>
        <div class="content">
            <p>Hola <strong><?= esc($leader['name']) ?></strong>,</p>
            <p>Se le ha asignado un nuevo requerimiento para gestión.</p>

            <div class="info-box">
                <p style="margin: 0;"><strong>Código de Trámite:</strong> <?= esc($document['document_code'] ?? "#" . $document['id']) ?></p>
                <p style="margin: 5px 0 0 0;"><strong>Título:</strong> <?= esc($document['title']) ?></p>
                <p style="margin: 5px 0 0 0;"><strong>Descripción:</strong> <?= esc($document['description'] ?? 'Sin descripción') ?></p>
                <?php if ($client): ?>
                <p style="margin: 5px 0 0 0;"><strong>Cliente:</strong> <?= esc($client['first_name'] . ' ' . $client['last_name']) ?></p>
                <p style="margin: 5px 0 0 0;"><strong>Cédula:</strong> <?= esc($client['cedula']) ?></p>
                <?php endif; ?>
                <p style="margin: 5px 0 0 0;"><strong>Fecha de Asignación:</strong> <?= date('d/m/Y H:i', strtotime($assignment['assigned_at'] ?? $document['created_at'])) ?></p>
                <?php if (!empty($assignment['due_date'])): ?>
                <p style="margin: 5px 0 0 0;"><strong>Fecha Límite:</strong> <?= date('d/m/Y', strtotime($assignment['due_date'])) ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($assignment['instructions'])): ?>
            <div class="instructions">
                <p style="margin: 0;"><strong>Instrucciones del Director:</strong></p>
                <p style="margin: 5px 0 0 0;"><?= nl2br(esc($assignment['instructions'])) ?></p>
            </div>
            <?php endif; ?>

            <p>Por favor tome acción sobre este requerimiento y mantenga el seguimiento adecuado.</p>
        </div>
        <div class="footer">
            <p>Este es un mensaje automático generado por el Sistema de Requerimientos de CNEL. Por favor, no respondas a este correo.</p>
            <p>&copy; <?= date('Y') ?> CNEL EP. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>