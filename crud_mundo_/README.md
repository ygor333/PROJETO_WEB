# CRUD Mundo

## Sobre o projeto

O CRUD Mundo é uma aplicação web para cadastro e gerenciamento de dados geográficos — continentes, países, cidades e governantes — com controle de acesso por login. O sistema permite registrar as relações entre essas entidades (um país pertence a um continente e possui um governante, uma cidade pertence a um país, etc.) e inclui uma camada de autenticação com bloqueio de conta após tentativas de login incorretas, troca obrigatória de senha no primeiro acesso e recuperação de senha por token.

## Funcionalidades

- Login com bloqueio automático após 3 tentativas incorretas
- Recuperação de senha por link com token temporário
- Troca de senha obrigatória no primeiro acesso
- Cadastro e desbloqueio de usuários do sistema
- Registro de log das ações de autenticação (login, falha, bloqueio, desbloqueio, troca de senha, logout)
- Cadastro, listagem, busca e exclusão de continentes
- Cadastro, listagem, busca e exclusão de países (vinculados a continente e governante)
- Cadastro, listagem, busca e exclusão de cidades (vinculadas a país e governante)
- Cadastro, listagem e exclusão de governantes
- Validação de formulários no navegador (campos obrigatórios, nomes só com letras, números positivos, confirmação antes de excluir)

## Tecnologias utilizadas

- PHP (mysqli)
- MySQL
- Bootstrap 5.3.3
- JavaScript
- Git
- GitHub

## Requisitos

- PHP 7.4 ou superior, com extensão `mysqli` habilitada
- MySQL ou MariaDB
- Servidor local, como XAMPP, WAMP ou Laragon

## Estrutura do projeto

```
crud_mundo_/
├── banco.sql                   
├── conexao.php                
├── login.php                   
├── js/
│   └── script.js              
└── pages/
    ├── login/                 
    │   ├── login.php
    │   ├── logout.php
    │   ├── esqueci_senha.php
    │   ├── resetar_senha.php
    │   ├── trocar_senha.php
    │   ├── verificar_sessao.php
    │   └── gerenciar_usuarios.php
    └── g.paises/               # Telas de cadastro 
        ├── index.php           # Menu principal
        ├── continentes.php
        ├── paises.php
        ├── cidades.php
        ├── governantes.php
        └── partials/
            ├── header.php
            └── footer.php
```

## Como executar

1. Clone o repositório.
2. Copie a pasta do projeto para o diretório público do seu servidor (ex.: `htdocs`).
3. Crie o banco de dados executando o script `banco.sql`:
   ```bash
   mysql -u root -p < banco.sql
   ```
4. Configure as credenciais de acesso ao banco em `conexao.php`.
5. Inicie o servidor e acesse `login.php` pelo navegador.

**Acesso inicial (usuário administrador padrão):**
- E-mail: `admin@crudmundo.com`
- Senha provisória: `Mudar@123` (troca obrigatória no primeiro login)

## Autor

Ygor Santana Ferreira
