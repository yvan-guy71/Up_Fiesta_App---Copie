<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { background: #4f46e5; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #4f46e5; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nouveau Message de Contact</h1>
        </div>
        <div class="content">
            <div class="field">
                <p class="label">De :</p>
                <p>{{ $contactData['name'] }} ({{ $contactData['email'] }})</p>
            </div>
            <div class="field">
                <p class="label">Sujet :</p>
                <p>{{ $contactData['subject'] }}</p>
            </div>
            <div class="field">
                <p class="label">Message :</p>
                <p style="white-space: pre-wrap;">{{ $contactData['message'] }}</p>
            </div>
        </div>
        <div class="footer">
            Ce message a été envoyé depuis le formulaire de contact de Up Fiesta.
        </div>
    </div>
</body>
</html>