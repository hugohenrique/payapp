## Payapp
Demonstração de uma plataforma de pagamento simplificada.

### Arquitetura
Foi utilizada uma implementação da **arquitetura hexagonal** com princípio/padrão arquitetural **CQS (Command Query Separation)**, para trazer mais clareza sobre o fluxo da aplicação.

A estrutura segue orientações que mesclam recomendações da arquitetura hexagonal com Domain Model Patterns, ficando:

```
/- Domain
|- Model
|- Repository
|- Service

/- Application
|- Command
|- CommandHandler
|- Event
|- EventHandler
- Service

/- Infrastructure
|- Doctrine
|- Framework
|- Http
```

Foi disponibilizado uma API Restfull, trazendo características do RMM (Richardson Maturity Model), claro, na proporção limitada para esse contexto desenvolvido.

## API

### POST /api/users
Para criar novos usuários

### POST /api/transactions
### POST /api/transfer
Para efetuar uma transferência financeira entre usuários.

### DELETE /api/transactions
Para que em caso de inconsistência, uma trasferência consiga ser revertida.