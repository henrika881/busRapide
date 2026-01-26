POST
/api/v1/payments/mobile-money
Initier un Paiement
Déclenche une demande de paiement (USSD Push) sur le téléphone du client pour Orange Money ou MTN MoMo.

Paramètres
operator

String

Requis
Code opérateur ("orange_money", "mtn_money").

phone

String

Requis
Numéro de téléphone du client (ex: 699000000).

amount

Integer

Requis
Montant à débiter.

currency

String

Requis
Devise locale (ex: "XAF", "XOF").

description

String

Référence ou description courte pour le relevé.

cURL
JavaScript
Python
PHP
Go

Copier
curl "https://api.nelsius.com/api/v1/payments/mobile-money" \
  -X POST \
  -H "X-API-KEY: sk_live_..." \
  -H "Content-Type: application/json" \
  -d '{
    "operator": "orange_money",//ou mobile_money
    "phone": "699000000",
    "amount": 5000,
    "currency": "XAF",
    "description": "Order #789"
  }'
Exemple de réponse
{
  "success": true,
  "message": "Paiement initié avec succès.",
  "data": {
    "reference": "TXN_7829102",
    "status": "pending"
  }
}




verifier le statut 



GET
/api/v1/payments/mobile-money/{reference}
Vérifier Statut Paiement
Vérifie l'état d'une transaction en temps réel. Cette méthode est recommandée pour confirmer le statut final si vous n'utilisez pas les webhooks.

Paramètres
reference

String (Path)

Requis
La référence (TXN_...) ou reference_id retournée lors de l'initiation.

cURL
JavaScript
Python
PHP
Go

Copier
curl "https://api.nelsius.com/api/v1/payments/mobile-money/TXN_7829102" \
  -H "X-API-KEY: sk_live_your_secret_key"
Exemple de réponse
{
  "success": true,
  "data": {
    "reference": "TXN_7829102",
    "status": "completed",
    "amount": 5000,
    "currency": "XOF",
    "paid_at": "2025-01-06T14:00:00Z",
    "metadata": {
       "operator_reference": "OM-83298"
    }
  }
}