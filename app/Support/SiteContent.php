<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Textos do site que aparecem em mais de um lugar: perguntas frequentes (página
 * e dados estruturados do Google) e as páginas de serviço (/servicos/{slug}).
 */
final class SiteContent
{
    /** @return list<array{0: string, 1: string}> pergunta e resposta */
    public static function faq(string $responseHours): array
    {
        return [
            ['Vocês atendem empresas pequenas?', 'Sim. Boa parte dos clientes são pequenas e médias empresas que dependem de um sistema interno e não têm time de TI. Você fala direto com quem vai programar.'],
            ['Vocês pegam sistemas que outra equipe começou?', 'Sim, é boa parte do que fazemos. Começamos com um diagnóstico do código e do servidor e dizemos com clareza o que vale manter, corrigir ou reescrever.'],
            ['Precisa parar o sistema para modernizar?', 'Não. A migração é feita em etapas, com o sistema antigo e o novo funcionando lado a lado até a troca.'],
            ['Como funciona o orçamento?', 'Tudo começa por um diagnóstico com escopo, prazo e preço fechados. Depois dele você recebe um plano em etapas, cada uma com o seu preço, e aprova uma de cada vez. Em emergências, como sistema parado, também atendemos por hora.'],
            ['Tem plano de manutenção mensal?', 'Sim. Um valor fixo por mês com horas de suporte, monitoramento, backup com restauração testada e atualizações de segurança. Bom para quem não pode ficar sem o sistema e não quer depender de um programador avulso.'],
            ['Vocês cuidam do servidor depois da entrega?', 'Sim. Cuidamos de onde o sistema roda, na nuvem ou num servidor da empresa, com backup, monitoramento e atualizações. Os acessos ficam no nome da sua empresa.'],
            ['Em quanto tempo vocês respondem?', "Em até {$responseHours} horas úteis com uma avaliação inicial. Mande pelo formulário ou pelo WhatsApp."],
        ];
    }

    /**
     * Páginas de serviço, uma por busca que o cliente faz no Google.
     *
     * @return array<string, array{icon: string, nav: string, title: string, description: string, headline: string, lead: string, signs: list<string>, steps: list<array{0: string, 1: string}>, included: list<string>, faq: list<array{0: string, 1: string}>}>
     */
    public static function services(): array
    {
        return [
            'migracao-de-sistemas-desktop-para-web' => [
                'icon' => 'monitor-up',
                'nav' => 'Sistema antigo para a web',
                'title' => 'Migração de sistema desktop (Delphi, VB, Access) para a web · NT Sistemas Web',
                'description' => 'Leve o sistema Delphi, VB6, Access ou PHP antigo da sua empresa para a web em etapas, sem parar a operação. Comece por um diagnóstico com preço fechado.',
                'headline' => 'Seu sistema Delphi, VB ou Access na web, sem parar a empresa.',
                'lead' => 'Muitos desses sistemas carregam anos de regra de negócio que ninguém documentou. O risco não é ele ser antigo: é depender de um computador, de uma versão do Windows ou de uma pessoa. Levamos para a web em etapas, com os dois funcionando lado a lado.',
                'signs' => [
                    'O sistema só roda num computador ou numa versão antiga do Windows',
                    'Ninguém consegue acessar de fora da empresa',
                    'Só uma pessoa sabe mexer no código',
                    'Cada atualização do Windows é um susto',
                ],
                'steps' => [
                    ['Mapear', 'Entender o que o sistema faz de verdade: telas, relatórios, regras e integrações.'],
                    ['Começar pelo que mais dói', 'A primeira parte que vai para a web é a que mais trava o dia a dia.'],
                    ['Migrar em etapas', 'Cada módulo novo entra no ar com os dados migrados e o antigo ainda funcionando.'],
                    ['Desligar o antigo', 'Só quando tudo estiver validado pela equipe que usa.'],
                ],
                'included' => ['Migração dos dados com conferência', 'Treinamento de quem usa', 'Acesso pelo navegador, de qualquer lugar', 'Código e servidor no nome da sua empresa'],
                'faq' => [
                    ['Precisa jogar o sistema antigo fora?', 'Não de uma vez. Os dois funcionam lado a lado durante a migração, e o antigo só é desligado quando a equipe validar o novo.'],
                    ['E se o código-fonte se perdeu?', 'Ainda dá. Partimos do banco de dados, das telas e de quem usa o sistema para reconstruir as regras.'],
                    ['Quanto custa?', 'Depende do tamanho do sistema. Por isso começamos com um diagnóstico com preço fechado: ele mostra as etapas e o preço de cada uma antes de você decidir.'],
                ],
            ],
            'manutencao-de-sistemas' => [
                'icon' => 'life-buoy',
                'nav' => 'Manutenção mensal',
                'title' => 'Manutenção de sistemas para empresas · NT Sistemas Web',
                'description' => 'Manutenção mensal do sistema da sua empresa: suporte, monitoramento, backup com restauração testada e atualizações de segurança, com quem programa.',
                'headline' => 'O sistema da sua empresa cuidado todo mês, antes de parar.',
                'lead' => 'Manutenção parece despesa até o dia em que o sistema para. Com um plano mensal, alguém acompanha o sistema, testa o backup, aplica as atualizações e resolve os problemas com prioridade.',
                'signs' => [
                    'Ninguém sabe quando o backup foi restaurado pela última vez',
                    'O programador que fez o sistema não atende mais',
                    'Pequenos ajustes ficam meses na fila',
                    'O servidor e as senhas estão no nome de outra pessoa',
                ],
                'steps' => [
                    ['Diagnóstico inicial', 'Levantamos o estado do sistema, do servidor e dos backups.'],
                    ['Ajustes urgentes', 'Corrigimos o que coloca a operação em risco.'],
                    ['Rotina mensal', 'Monitoramento, atualizações e restauração de backup testada.'],
                    ['Relatório', 'Todo mês, uma página com o que foi feito e o que vem a seguir.'],
                ],
                'included' => ['Horas de suporte por mês', 'Monitoramento do sistema e do servidor', 'Backup com restauração testada', 'Atualizações de segurança', 'Prioridade quando algo para'],
                'faq' => [
                    ['Tem fidelidade?', 'Não. O plano é mensal e pode ser cancelado com aviso prévio.'],
                    ['As horas que não uso acumulam?', 'Isso é combinado na proposta, de acordo com o tamanho do sistema.'],
                    ['Vocês mantêm sistemas que não fizeram?', 'Sim, é boa parte do trabalho. Começamos por um diagnóstico para entender o que existe.'],
                ],
            ],
            'diagnostico-de-sistema' => [
                'icon' => 'search-code',
                'nav' => 'Diagnóstico de sistema',
                'title' => 'Diagnóstico de sistema travando, lento ou sem manutenção · NT Sistemas Web',
                'description' => 'Sistema travando, lento ou sem ninguém para mexer? O diagnóstico mostra o que manter, corrigir ou reescrever, em ordem de risco, com escopo, prazo e preço fechados.',
                'headline' => 'Entenda o que o seu sistema tem antes de decidir o que fazer.',
                'lead' => 'O diagnóstico é a porta de entrada: analisamos o código, o banco e o servidor e entregamos um relatório em linguagem de negócio, com o que manter, corrigir e reescrever, em ordem de risco.',
                'signs' => [
                    'O sistema trava ou fica lento em horários de pico',
                    'Cada ajuste pequeno quebra outra coisa',
                    'A equipe voltou para a planilha',
                    'Alguém sugeriu reescrever tudo do zero',
                ],
                'steps' => [
                    ['Conversa', 'Entendemos o que o sistema faz e o que mais incomoda.'],
                    ['Análise', 'Leitura do código, do banco de dados e do servidor, e teste de restauração do backup.'],
                    ['Relatório', 'Problemas em ordem de risco e recomendação: manter, corrigir ou reescrever.'],
                    ['Plano', 'Etapas com prazo e preço. Você decide se segue e com quem.'],
                ],
                'included' => ['Relatório em linguagem de negócio', 'Riscos de segurança e de dependência de pessoas', 'Teste de restauração do backup', 'Plano em etapas com prazo e preço', 'Reunião para apresentar o resultado'],
                'faq' => [
                    ['O diagnóstico mexe no sistema em produção?', 'Não. A análise é feita sem alterar nada no sistema que a empresa usa.'],
                    ['Sou obrigado a fazer o projeto com vocês depois?', 'Não. O relatório é seu e serve para decidir com qualquer equipe.'],
                    ['Quanto tempo leva?', 'Depende do tamanho do sistema; o prazo é fechado junto com o preço, antes de começar.'],
                ],
            ],
        ];
    }
}
