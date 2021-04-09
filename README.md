<p align="center"><img src="https://deploy.agapesolucoes.com.br/media/logos/AGP/logo-blue.svg" width="400"></p>

# Package Two Factor

Package para autenticaçào de dois fatores.

---

## Menu

- [Git do projeto](#git-do-projeto)
- [Fórum de discução](#frum-de-discuo)
- [Requisitos](#requisitos)
- [Instalação](#instalao)
- [Utilizando](#utilizando)
    - [Criar](#criar)
    - [Exibindo QRCode](#exibindo-qrcode)
    - [Validar](#validar)
    - [Consultar](#consultar)
    - [Desativar](#desativar)

### Git do projeto
[AGP/package-twofactor](https://git.agapesolucoes.com.br/AGP/package-twofactor)

### Fórum de discução
[Fórum AGP](https://www.agapesolucoes.com.br/forum)

### Requisitos

- PHP 7.2+

### Instalação

```bash
composer require  agp/twofactor
```

O segundo passo é verificar se as variáveis que são utilizadas nesse arquivo ja estão setadas no .env.

``` yaml
API_AGPADMIN=value
```

### Utilizando

Como gerar e usar a autenticação de dois fatores

#### Criar

Uma chave secreta é gerada para o seu usuário e salva pela ID:
```php
use Agp\TwoFactor\GoogleAuthentication;
    
$googleAuthenticator = new GoogleAuthentication();

return $googleAuthenticator->create($adm_pessoa_id);
```

```json
{
    "data": {
        "adm_pessoa_id": 2,
        "verificado": 0,
        "secret_key": "LIZGIMFYTHSDXKBG7TTR4SMLEWTIBNMYNZSO4B..."
    }
}
```

Caso o usuário ja possua uma chave e a mesma esteja verifica o campo **secret_key** vem "null".

#### Exibindo QRCode

```html
<img src="{{ (new \chillerlan\QRCode\QRCode)->render($googleAuthenticator->secret_key) }}" alt="">
```
Como requisito o **chillerlan QrCode** já vem em nosso pacote, mas você pode utilizar o gerador de QrCode de sua preferencia.

#### Validar

```php
$secret = $request->input('secret');

$valid = (new GoogleAuthentication())->verify($adm_pessoa_id, $secret);
```
Caso no método verify não ocorra nenhuma exception o $valid se encontra com o valor de "true";

#### Consultar

```php
use Agp\TwoFactor\GoogleAuthentication;
    
$googleAuthenticator = new GoogleAuthentication();
    
return $googleAuthenticator->get($adm_pessoa_id);
```

#### Desativar

Para que a autenticação seja desabilitada é necessario enviar um código.

```php
$secret = $request->input('secret');

(new GoogleAuthentication())->destroy($adm_pessoa_id, $secret);
```

### Copyright

AGP @ 2020

