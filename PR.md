# Relatório Técnico - Refatoração e Implementação de Novos Recursos

## Introdução

Este documento descreve as principais alterações, refatorações e
implementações realizadas no sistema, visando melhorar a arquitetura,
experiência do usuário e manutenibilidade do código. As mudanças foram
implementadas seguindo boas práticas de desenvolvimento e padrões de
projeto estabelecidos.

## 1. Refatoração do Sistema de Invoices e Products

### 1.1 Reestruturação do Relacionamento Invoice-Product

Identifiquei que o sistema estava utilizando uma tabela pivot items para
relacionar invoices e produtos, o que não é a abordagem mais adequada
para este caso de uso. Realizei as seguintes alterações:

-   Remoção da tabela pivot items: Eliminei o relacionamento indireto
    que adicionava complexidade desnecessária\
-   Relacionamento direto: Implementei o relacionamento direto entre
    invoices e products, simplificando queries e facilitando a
    manutenção\
-   Correção do tipo de dado de preço: Alterei o campo de preço de
    integer para decimal, garantindo precisão nos cálculos financeiros e
    evitando problemas de arredondamento

### 1.2 Interface de Adição de Produtos

Desenvolvi uma nova interface que permite a seleção de produtos a partir
de uma lista pré-cadastrada, eliminando a necessidade de digitar item
por item manualmente. Isso proporciona:

-   Maior agilidade no processo de criação de invoices\
-   Redução de erros de digitação\
-   Validação automática de produtos existentes\
-   Melhor experiência do usuário

## 2. Implementação do Sistema de Status

### 2.1 Normalização da Base de Dados

Anteriormente, o status das invoices era armazenado como string
diretamente na tabela, o que violava princípios de normalização e
dificultava manutenção. Implementei:

-   Tabela status: Criei uma tabela dedicada para gerenciar os status do
    sistema\
-   Relacionamento foreign key: Estabeleci o relacionamento adequado
    entre invoices e status\
-   Migration estruturada: Desenvolvi a migration incluindo os status
    padrão e suas respectivas validações

**Benefícios desta abordagem:**

-   Facilita alteração e adição de novos status\
-   Permite adicionar metadados aos status (cores, ícones, permissões)\
-   Evita inconsistências de dados\
-   Melhora performance em queries com filtros de status

## 3. Sistema de Controle de Acesso (ACL)

### 3.1 Implementação de Roles

Reestruturei completamente o sistema de controle de acesso, que
anteriormente utilizava verificações baseadas em e-mails hardcoded no
arquivo de configuração. As alterações incluem:

**Migrations:**

-   Criei a migration da tabela roles com estrutura para gerenciar
    diferentes níveis de acesso\
-   Adicionei o campo role_id na tabela users, estabelecendo o
    relacionamento

**Seeders:**

-   RoleSeeder: Implementei para popular a tabela com roles padrão (god,
    admin, user)\
-   UserSeeder: Refatorei a lógica de criação do primeiro usuário, que
    estava inadequadamente no DatabaseSeeder\
-   Criei múltiplos usuários de teste, sendo apenas um com role god para
    testes de permissões

### 3.2 Middleware e Autorização

**FortifyServiceProvider:**

-   Implementei função customizada para sobrescrever o redirecionamento
    pós-login baseado no role do usuário\
-   Usuários com role god são redirecionados para área administrativa\
-   Demais usuários são direcionados para dashboard padrão

**FeatureAuthorizationMiddleware:**

-   Refatorei para validar tipo de usuário através do relacionamento com
    roles\
-   Removi dependência de verificação por e-mail no arquivo de
    configuração\
-   Organizei os redirecionamentos de rotas de forma mais elegante e
    manutenível

## 4. CRUD de Usuários (Área Administrativa)

Desenvolvi um CRUD completo para gerenciamento de usuários, acessível
apenas para usuários com role god. Implementações incluem:

**Sistema de Notificações:**

-   Toasts: Integrei sistema de toasts para mensagens de sucesso e erro\
-   SweetAlert2: Implementei para confirmações críticas, substituindo os
    alerts JavaScript nativos que estavam sendo utilizados

**Confirmação de Exclusão:**

-   Substituí o sistema antigo de confirmação JavaScript por
    SweetAlert2\
-   Implementei confirmação em duas etapas para ações destrutivas\
-   Adicionei feedback visual durante o processo de exclusão

**Funcionalidades:**

-   Listagem com paginação funcional\
-   Filtros e busca\
-   Validações robustas no backend e frontend\
-   Logs de auditoria

## 5. Implementação de Modais

### 5.1 Motivação

Para melhorar a experiência do usuário e reduzir a quantidade de
arquivos e navegações desnecessárias, implementei um sistema de modais
para operações de CRUD. Benefícios:

-   Redução do número de views e rotas\
-   Operações mais rápidas sem reload completo da página\
-   Melhor fluxo de trabalho\
-   Interface mais moderna e responsiva

### 5.2 Aplicação nos CRUDs

**Clientes:**

-   Modais para criação e edição\
-   Duplicação proposital do controller (backend/frontend) para evitar
    conflitos com Inertia.js\
-   Justificativa: O Inertia retorna diretamente arquivos Vue, e manter
    controllers separados previne problemas de roteamento e mantém
    responsabilidades bem definidas

**Invoices:**

-   Modais para todas operações CRUD\
-   Implementação de SoftDeletes para manter histórico\
-   Interface de seleção de produtos integrada ao modal\
-   Justificativa do SoftDeletes: Permite auditorias futuras e geração
    de relatórios históricos sem perder dados

**Teams:**

-   CRUD completo com modais\
-   Seguindo o mesmo padrão estabelecido para consistência

## 6. Sistema de Paginação

Corrigi problemas existentes no sistema de paginação que não estava
funcionando adequadamente nos CRUDs. Implementei:

-   Paginação consistente em todas as listagens\
-   Configuração de itens por página\
-   Navegação intuitiva\
-   Manutenção de filtros durante navegação entre páginas

## 7. Considerações Técnicas e Arquiteturais

### 7.1 Duplicação de Código (Controllers)

Optei conscientemente por duplicar controllers para separar lógica de
backend (API/dados) e frontend (Inertia/Vue), pois:

-   Inertia.js possui fluxo específico de renderização\
-   Evita conflitos de rotas e retornos\
-   Mantém separação clara de responsabilidades\
-   Facilita manutenção futura por diferentes desenvolvedores

### 7.2 SoftDeletes

A implementação de SoftDeletes em tabelas críticas como invoices
garante:

-   Conformidade com possíveis requisitos de auditoria\
-   Capacidade de recuperação de dados\
-   Histórico completo para relatórios\
-   Sem impacto nas queries principais (utilizando scopes globais)

### 7.3 Normalização de Dados

A criação da tabela de status e o relacionamento adequado de products
demonstram compromisso com:

-   Integridade referencial\
-   Facilidade de manutenção\
-   Escalabilidade do sistema\
-   Padrões de banco de dados relacionais

## Conclusão

As refatorações e implementações realizadas elevam significativamente a
qualidade técnica do sistema, estabelecendo uma base sólida para futuras
expansões. A aplicação de design patterns, boas práticas e foco em
experiência do usuário resultam em um sistema mais robusto, manutenível
e profissional.
