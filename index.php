<?php include 'conexao/conexao.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Agendamento de Aulas - SENAI União da Vitória</title>
    <link rel="stylesheet" href="css/style.css">
    </style>
</head>

<body>
    <header>
        <h1>📅 Agendamento de Aulas - SENAI União da Vitória</h1>
    </header>

    <main>
        <table id="tabela-agenda">
            <thead>
                <tr>
                    <th>Professor</th>
                    <th>Turno</th>
                    <th>Seg</th>
                    <th>Ter</th>
                    <th>Qua</th>
                    <th>Qui</th>
                    <th>Sex</th>
                    <th>Sab</th>
                    <th>Dom</th>
                </tr>
            </thead>
            <tbody id="corpo-tabela">
                <!-- Preenchido via JS -->
            </tbody>
        </table>
    </main>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <h2>Agendar Aula</h2>
            <form id="formAula">
                <input type="hidden" id="professor_id">

                <label>Dia da Semana:</label>
                <select id="dia_semana">
                    <option>Seg</option>
                    <option>Ter</option>
                    <option>Qua</option>
                    <option>Qui</option>
                    <option>Sex</option>
                    <option>Sab</option>
                    <option>Dom</option>
                </select>

                <label>Turno:</label>
                <select id="turno">
                    <option>Manhã</option>
                    <option>Tarde</option>
                    <option>Noite</option>
                </select>

                <label>Descrição:</label>
                <input type="text" id="descricao" placeholder="Ex: Eletromecânica, ADM...">

                <label>Cor:</label>
                <input type="color" id="cor" value="#c3002f">

                <div class="botoes">
                    <button type="submit">Salvar</button>
                    <button type="button" id="cancelar">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function carregarAgenda() {
            fetch('api/listar_aulas.php')
                .then(res => res.json())
                .then(aulas => {
                    console.log("Aulas recebidas:", aulas);
                    const corpo = document.getElementById('corpo-tabela');
                    corpo.innerHTML = '';
                    const professores = {};

                    aulas.forEach(aula => {
                        if (!professores[aula.professor]) {
                            professores[aula.professor] = {
                                Manhã: {},
                                Tarde: {},
                                Noite: {}
                            };
                        }
                        professores[aula.professor][aula.turno][aula.dia_semana] = aula;
                    });

                    for (const nome in professores) {
                        ['Manhã', 'Tarde', 'Noite'].forEach(turno => {
                            const linha = document.createElement('tr');

                            if (turno === 'Manhã') {
                                const tdNome = document.createElement('td');
                                tdNome.rowSpan = 3;
                                tdNome.textContent = nome;
                                linha.appendChild(tdNome);
                            }

                            const tdTurno = document.createElement('td');
                            tdTurno.textContent = turno;
                            linha.appendChild(tdTurno);

                            ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Dom'].forEach(dia => {
                                const td = document.createElement('td');
                                const aula = professores[nome][turno][dia];
                                if (aula) {
                                    td.textContent = aula.descricao;
                                    td.style.backgroundColor = aula.cor;
                                    td.style.color = '#fff';
                                } else {
                                    td.textContent = '+';
                                    td.classList.add('vazio');
                                    td.addEventListener('click', () => abrirModal(nome, turno, dia));
                                }
                                linha.appendChild(td);
                            });

                            corpo.appendChild(linha);
                        });
                    }
                })
                .catch(err => console.error('Erro ao carregar aulas:', err));
        }


        function abrirModal(professor, turno, dia) {
            document.getElementById('modal').style.display = 'flex';
            document.getElementById('descricao').value = '';
            document.getElementById('dia_semana').value = dia;
            document.getElementById('turno').value = turno;
            document.getElementById('professor_id').value = professor;
        }

        document.getElementById('cancelar').addEventListener('click', () => {
            document.getElementById('modal').style.display = 'none';
        });
    </script>
</body>

</html>