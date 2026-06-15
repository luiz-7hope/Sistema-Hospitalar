# API do MediCare

Esta pasta contém a camada PHP para integração com MySQL.

Endpoints iniciais:
- `POST /api/auth/register.php` para cadastro
- `POST /api/auth/login.php` para autenticação
- `GET /api/faturas/listar.php` para listar faturas persistidas
- `POST /api/faturas/sincronizar.php` para recalcular faturas a partir dos registros de uso
- `POST /api/faturas/marcar_pago.php` para marcar uma fatura como paga
- `POST /api/faturas/marcar_pendente.php` para marcar uma fatura como pendente

Banco esperado:
- `medicare_system`

Importe primeiro o arquivo `database/schema.sql` no MySQL.
