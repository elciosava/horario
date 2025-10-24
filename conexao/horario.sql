-- Tabela de professores (já deve existir)
CREATE TABLE IF NOT EXISTS professores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

-- Nova tabela: tipos de aula
CREATE TABLE IF NOT EXISTS tipos_aula (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sigla CHAR(3) NOT NULL,
    descricao VARCHAR(100) NOT NULL,
    professor_id INT,
    FOREIGN KEY (professor_id) REFERENCES professores(id)
);

-- Tabela de aulas agendadas
CREATE TABLE IF NOT EXISTS aulas_agendadas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_aula_id INT,
    dia_semana ENUM('Seg','Ter','Qua','Qui','Sex','Sab','Dom'),
    turno ENUM('Manhã','Tarde','Noite'),
    cor VARCHAR(20),
    FOREIGN KEY (tipo_aula_id) REFERENCES tipos_aula(id)
);
