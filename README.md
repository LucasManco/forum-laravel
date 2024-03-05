# Api Forum Laravel

## Instalação

Para executar o projeto primeiramente deve-se copiar o arquivo .env.example para o arquivo .env onde as váriaveis de ambiente são definidas. Para isso basta rodar o seguinte codigo na raiz do projeto:

``` 
cp .env.example .env
```

Em seguida deve-se inicializar o laravel sail para que os containers sejam montados. Isso é possivel através do seguinte código:

```
vendor/bin/sail up -d
```

Após devemos rodar as migrations para criar as tabelas no banco de dados:

```
vendor/bin/sail artisan migrate
```

Com isso já teremos o sistema funcionando corretamente. 

## Testes

Durante o desenvolvimento desta api foi utilizada a metodologia TDD (Test Driven Development). Para executar os testes criados durante este processo basta rodar o codigo abaixo na raiz do projeto:

```
vendor/bin/sail artisan test
```

## Documentação API
O arquivo Forum-Laravel.postman_collection.json possui a documentação da api para ser importada no Postman.

