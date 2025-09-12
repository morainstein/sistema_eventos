<h1>Projeto portfólio: Sistema de gestão de eventos.</h1>

<h2>Sobre</h2>
  Este projeto tem como único objetivo demostrar minhas competências de desenvolvimento back-end, portanto, nem todas as funcionalidades estarão 100% implementadas (afinal de contas, qualquer sistema sempre pode melhorar, não é mesmo?!), como por exemplo, rotas para operações simples, ou funcionalidades onde regras de negócio poderiam ser aplicadas de maneiras muito distintas, dependendo do negócio.  

  <br>
  O foco do projeto é na demonstração prática, de técnicas de desenvolvimento que já exigem certo conhecimento e experiência, como Docker ou eventos assincronos, por exemplo.

<h3>Competências adiquiridas e técnicas envolvidas neste projeto:</h3>
  
  - PHP/Laravel
      - Gerenciamento de extensões com PECL
      - Xdebug para debugger e coverage de testes
      - Configuração de php.ini para ambiente de produção
      - Utilização do PHP-FPM para integrar com NGINX via FastCGI
      - Utilização de módulos Laravel:
          - Sanctum para autenticação de APIs via token
          - Requests para validação de dados
          - Models com UUIDs, escopos de querys sql globais e locais e casts para conversões de tipo  
          - Observers para associar comportamentos as models
          - Events e Listeners para desacoplar lógica do sistema conforme suas funcionalidades
          - Factories e Seeders para facilitar testes e popular o banco com dados iniciais
          - Storage para armazenamento em bucket s3
          - Mocks para dublar testes 
          - Testes unitários, e de integração com coverage de 81%
          - Classes de serviço para efetuar as regras da aplicação desacoplada dos controladores

  -  Back-end
      - API Rest
      - Webhook para intregração com gateway de pagamento (Paggue)
      - Validação de webhooks assinados para segurança da aplicação
      - Eventos assincronos para melhoria do tempo de resposta da aplicação
      - Envio de emails com serviço externo
      - boas práticas com SOLID, Fail fast e Dont Repeat Yourself

  - Infraestrutura
      - Build de imagens com Dockerfile
      - Integração de containers com docker-compose
      - Api gateway com [NGINX](https://hub.docker.com/r/nginx/nginxaas-loadbalancer-kubernetes)
      - balanceamento de carga com prioridade para a aplicação que não processa os eventos assincronos salvo no banco de dados
      - Servidor smtp com [axllent/mailpit](https://hub.docker.com/r/axllent/mailpit)
      - Bucket s3-like com [quay.io/minio/minio](https://hub.docker.com/r/minio/minio)
      - Banco de dados com [MySQL](https://hub.docker.com/_/mysql)

<h2>Rodando o projeto</h2>

<h3>Baixando o projeto</h3>

  ```bash
  git clone https://github.com/morainstein/sistema_eventos.git
  ```

<h3>Configuração do ambiente do projeto</h3>



  O projeto é baixado praticamente pronto, restando apenas as configurações das variáveis de ambiente no arquivo **.env** .

  Para facilitar o processo, o arquivo [.env.example](.env.example) já vem com um exemplo quase funcional, necessitando alterar de fato somente duas variáveis.

  - **APP_KEY**  
    Esta é a chave da aplicação, necessário em qualquer aplicação laravel, pois ele a utiliza para suas funcões criptográficas.

    Para gerar é simples, através do comando `php artisan key:generate` na pasta raiz

  - **APP_URL**  
    **!Esta variável é imporante para a integração com o sistema de pagamento.!**  

    Tendo em vista que a confirmação do gateway de pagamento ocorre via integração com webhook, torna-se necessário que esta aplicação esteja acessível na internet, para isso, recomendo a utilização do [ngrok](https://ngrok.com), que também pode ser executado via [Docker](https://ngrok.com/docs/using-ngrok-with/docker/), ou baixado diretamente na máquina.  

    O Ngrok cria um túnnel de conexão entre a porta escolhida na sua máquina, e os servidores deles, disponibilizando o uri de acesso público para a sua máquina. Este uri será necessário nesta variável de ambiente (**APP_URL**) para que o gateway de pagamento possa enviar requisições de confirmação de pagamento.


<h3>Rotas<h3>

  As rotas do projeto podem ser encontradas em:
  - [Link da coleção Postman](https://.postman.co/workspace/My-Workspace~2a47f7c6-7f48-450e-8ca1-fe4c5d8be9f9/collection/41276021-25cb46cc-42f4-41b0-bd80-f62d88269299?action=share&creator=41276021&active-environment=41276021-acfc1749-64a6-4e08-aab1-ecdaae4180c3)
  - [Json exportado pelo Postman](/sistema_eventos.postman_collection.json)

<h3>Executando a aplicação</h3>

  **Todos os comandos a seguir devem ser executados na pasta raiz do projeto**

  O comando a seguir constrói, configura e executa os containers através do docker-compose.yml:
  ```bash
  docker-compose up -d --build
  ```

  Testando o ambiente
  ```bash
  docker-compose exec -it app1 bash /app/test-init.sh
  ```

  A aplicação foi construída utilizando um load balancer com distribuição de carga 2:1 para o container app1. Foi pensada dessa forma para que não sobrecarregue o app2, que deverá executar o worker de jobs assíncronos (tendo em vista que não haverá tantos eventos assincronos na aplicação, seria disperdício de processamento e memória executar o worker nos dois containers) 

  Para facilitar o processo de inicialização do sistema, você só precisa rodar esses comandos na pasta raiz do projeto

  <small>Para detalhes, basta conferir os scripts em /infra/app/</small>

  ```bash
  docker-compose exec -d app1 bash /app/app1-init.sh
  ```


  ```bash
  docker-compose exec -it app2 bash /app/app2-init.sh
  ```

  Agora é só utilizar a aplicação, conforme a coleção do postman anexada no projeto.  
  <small>
    **Os dados de exemplo dos usuários inseridos no banco através do script *prod-init.sh* coincidem com as coleções do postman**
  </small>

<h3>Considerações importantes para rodar o projeto</h3>

  Como o projeto comportaria o cadastro de vários promotores de eventos, onde cada promotor criaria seu próprio evento e receberia o valor dos ingressos em sua conta, não faria sentido cadastrar credenciais fixas para receber o pagamento, fazendo-se necessário o cadastro das credenciais do gateway de pagamento escolhido [Paggue](https://paggue.io) de cada promotor, para gerenciar esta integração.

  Ressalto uma atenção especial para as rotas de:
  - **Cadastro de evento**: pois o promotor precisa ter credenciais cadastradas pra poder criar um evento, tendo em vista que não teria como vender um ingresso, sem uma conta para cair o pagamento.
  - **Cadastro das credenciais do promotor**: pois há integração com o gateway de pagamento para cadastrar o webhook no qual irá receber a confirmação de pagamento do ingresso.
  - **Apagar credenciais do promotor**: pos também há integração com o gateway de pagamento para apagar o webhook cadastrado.

  O sistema tentará fazer o cadastro das credenciais Paggue e, se forem inválidas, o sistema irá naturalmente falhar. Se quiser desativar esta integração, retire as linhas 32 e 46 do arquivo *[PaggueCredentialsController](/app/Http/Controllers/PaggueCredentialsController.php)*, mas tendo ciência de que irá quebrar uma das funcionalidades principais da aplicação (não irá conseguir compra um ingresso, por exemplo, pois não terá como cadastrar a conta no gateway de pagamento).

  Em resumo, para testar completamente o sistema, terá que cadastrar credenciais válidas que se integram a [API da Paggue](http://go.paggue.io/dev)

  Para uma análise mais aprofundada, consulte as rotas *store* e *destroy* do controllador *[PaggueCredentialsController](/app/Http/Controllers/PaggueCredentialsController.php)*