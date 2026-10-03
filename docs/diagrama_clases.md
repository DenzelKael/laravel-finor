# Diagrama de Clases - Módulo Clientes y Suscripciones

```mermaid
classDiagram
    class Client {
        +int id
        +string name
        +string email
        +string phone
        +string address
        +string status
        +DateTime created_at
        +DateTime updated_at
    }

    class Plan {
        +int id
        +string name
        +float price
        +int duration_in_days
        +DateTime created_at
        +DateTime updated_at
    }

    class Subscription {
        +int id
        +int client_id
        +int plan_id
        +DateTime start_date
        +DateTime end_date
        +string status
        +DateTime created_at
        +DateTime updated_at
    }

    class Payment {
        +int id
        +int subscription_id
        +float amount
        +DateTime payment_date
        +DateTime created_at
    }

    %% Relaciones
    Client "1" --> "0..*" Subscription : tiene
    Plan "1" --> "0..*" Subscription : pertenece a
    Subscription "1" --> "0..*" Payment : genera