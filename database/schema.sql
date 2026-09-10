-- For Champions - schema inicial
-- Senha padrao admin: admin123

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS admin_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    chave VARCHAR(100) NOT NULL PRIMARY KEY,
    valor TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(180) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    categoria VARCHAR(80) NOT NULL,
    preco DECIMAL(10,2) NOT NULL DEFAULT 0,
    descricao TEXT NOT NULL,
    imagem VARCHAR(255) DEFAULT NULL,
    destaque TINYINT(1) NOT NULL DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    ordem INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(180) NOT NULL UNIQUE,
    titulo VARCHAR(180) NOT NULL,
    conteudo MEDIUMTEXT NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO admin_users (username, password_hash) VALUES
('admin', '$2y$12$e92MSF8F9CiLObU82dkf6.iLmFYATxBiUMlR8qLm3XAmmudnim.Oq');

INSERT INTO settings (chave, valor) VALUES
('whatsapp', '(51) 99188-6097'),
('email', 'contato@forchampions.com.br'),
('telefone', '(51) 99188-6097'),
('cnpj', '00.000.000/0000-00'),
('empresa', 'For Champions'),
('endereco_retirada', 'Retirada disponivel em Canoas/RS'),
('copyright', 'For Champions. Todos os direitos reservados.'),
('faq_json', '[{"pergunta":"Voces entregam para todo o Brasil?","resposta":"Sim. Enviamos para todo o territorio nacional com prazo e valor de frete informados no atendimento via WhatsApp."},{"pergunta":"Posso personalizar os uniformes?","resposta":"Sim. Trabalhamos com estampas, nomes, numeros, escudos e cores conforme a identidade do seu time ou empresa."},{"pergunta":"Existe pedido minimo?","resposta":"Depende do produto e do tipo de personalizacao. Consulte no WhatsApp para receber a orientacao exata do seu pedido."},{"pergunta":"Qual o prazo de producao?","resposta":"O prazo varia conforme quantidade, modelo e personalizacao. Informamos o prazo estimado antes da confirmacao do pedido."},{"pergunta":"Quais formas de pagamento sao aceitas?","resposta":"Aceitamos cartao de credito em ate 12x e pagamento a vista. Detalhes sao passados no atendimento."},{"pergunta":"Posso retirar em Canoas?","resposta":"Sim. Ha opcao de retirada em Canoas/RS quando o pedido estiver pronto."},{"pergunta":"E se houver problema com meu pedido?","resposta":"Entre em contato pelo WhatsApp ou e-mail informando o numero do pedido. Nossa equipe analisa e orienta a solucao."}]');

INSERT INTO products (nome, slug, categoria, preco, descricao, imagem, destaque, ativo, ordem) VALUES
('Bandeira 100x140', 'bandeira-100x140', 'Acessorios', 45.00, 'Bandeira personalizada 100x140 cm para torcida, eventos e times.', 'assets/images/products/bandeira.jpg', 0, 1, 1),
('Regata personalizada', 'regata-personalizada', 'Camisetas', 54.90, 'Regata esportiva personalizada com opcoes Traditional e Slim Fit.', 'assets/images/products/regata.jpg', 0, 1, 2),
('Meiao de futebol', 'meiao-de-futebol', 'Acessorios', 16.00, 'Meiao de futebol personalizado para times e escolinhas.', 'assets/images/products/meiao.jpg', 0, 1, 3),
('Camiseta infantil modelo Champions', 'camiseta-infantil-champions', 'Camisetas', 69.90, 'Camiseta infantil modelo Champions com personalizacao completa.', 'assets/images/products/camiseta-infantil.jpg', 0, 1, 4),
('Camiseta plus size', 'camiseta-plus-size', 'Camisetas', 84.90, 'Camiseta plus size com conforto e personalizacao para todos os tamanhos.', 'assets/images/products/camiseta-plus-size.jpg', 0, 1, 5),
('Casaco personalizado', 'casaco-personalizado', 'Moletons e Casacos', 140.00, 'Casaco personalizado para equipes, empresas e eventos esportivos.', 'assets/images/products/casaco.jpg', 0, 1, 6),
('Corta-vento', 'corta-vento', 'Moletons e Casacos', 89.90, 'Corta-vento leve e resistente, ideal para treinos e viagens.', 'assets/images/products/corta-vento.jpg', 0, 1, 7),
('Canguru personalizado', 'canguru-personalizado', 'Moletons e Casacos', 120.00, 'Canguru personalizado com capuz e estampa exclusiva.', 'assets/images/products/canguru.jpg', 0, 1, 8),
('Kit Champions camiseta + calcao', 'kit-champions-camiseta-calcao', 'Kits', 130.00, 'Kit completo camiseta + calcao modelo Champions.', 'assets/images/products/kit-champions.jpg', 1, 1, 9),
('Camiseta modelo Champions', 'camiseta-modelo-champions', 'Camisetas', 74.90, 'Camiseta adulto modelo Champions com alta qualidade de estampa.', 'assets/images/products/camiseta-champions.jpg', 1, 1, 10),
('Kit Prata camiseta + calcao', 'kit-prata-camiseta-calcao', 'Kits', 89.90, 'Kit Prata com camiseta e calcao personalizados.', 'assets/images/products/kit-prata.jpg', 1, 1, 11),
('Calca infantil', 'calca-infantil', 'Calcas', 105.00, 'Calca infantil personalizada para times e escolinhas.', 'assets/images/products/calca-infantil.jpg', 1, 1, 12),
('Moletom infantil', 'moletom-infantil', 'Moletons e Casacos', 135.00, 'Moletom infantil confortavel com personalizacao.', 'assets/images/products/moletom-infantil.jpg', 1, 1, 13),
('Saco grande de uniformes', 'saco-grande-de-uniformes', 'Acessorios', 35.00, 'Saco grande para transporte e organizacao de uniformes.', 'assets/images/products/saco-uniformes.jpg', 1, 1, 14);
