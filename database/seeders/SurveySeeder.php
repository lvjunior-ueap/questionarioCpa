<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Survey;
use App\Models\Audience;
use App\Models\Dimension;
use App\Models\Question;

class SurveySeeder extends Seeder
{
    
    public function run(): void
    {
        $survey = Survey::create([
            'title' => 'Questionário de Avaliação Institucional – CPA UEAP 2025',
            'description' => 'Instrumento de autoavaliação institucional – Ano de referência 2025',
            'active' => true,
        ]);

        $audiences = [
            'docente'  => 'Docente',
            'discente' => 'Discente',
            'tecnico'  => 'Técnico Administrativo',
            'egresso'  => 'Egresso',
            'externo'  => 'Comunidade Externa',
        ];

        foreach ($audiences as $slug => $name) {
            $audience = Audience::create([
                'name' => $name,
                'slug' => $slug,
                'intro_text' => "Questionário destinado a {$name}.",
            ]);

            $this->seedDimensions($survey, $audience);
        }
    }

    private function seedDimensions(Survey $survey, Audience $audience): void
    {
        $dimensions = [

            // DIMENSÃO I
            [
                'title' => 'Dimensão I – Missão e Plano de Desenvolvimento Institucional (PDI)',
                'questions' => [
                    'Conheço a missão da UEAP.',
                    'A missão institucional é clara.',
                    'A missão institucional orienta as decisões da gestão no meu setor/unidade.',
                    'Conheço o plano de desenvolvimento anual.',
                    'Conheço o PDI vigente.',
                    'As ações desenvolvidas pelo meu setor/unidade estão alinhadas às diretrizes e metas previstas no PDI.',
                    'O PDI é utilizado como instrumento efetivo de planejamento.',
                ],
            ],

            // DIMENSÃO II
            [
                'title' => 'Dimensão II – Política para o ensino, a pesquisa, a pós-graduação e a extensão',
                'questions' => [
                    'As políticas e as estratégias de ensino, pesquisa e extensão da UEAP são executadas de forma interligada.',
                    'As políticas de ensino da UEAP são claras.',
                    'As políticas de pós-graduação e pesquisa da UEAP são claras.',
                    'As políticas de extensão da UEAP são claras.',
                    'O comitê de ensino é atuante - CEG.',
                    'O comitê de pesquisa e pós-graduação é atuante - CPPG.',
                    'O comitê de extensão é atuante - CAEXT.',
                    'O Programa de bolsas de monitoria atende à comunidade acadêmica de forma satisfatória - PROMONITORIA.',
                    'O Programa de bolsas de iniciação à docência atende à comunidade acadêmica de forma satisfatória - PIBID.',
                    'O Programa de bolsas de iniciação científica atende à comunidade acadêmica de forma satisfatória - PIBIC.',
                    'O Programa de bolsas de extensão atende à comunidade acadêmica de forma satisfatória - PIBEXT.',
                    'A UEAP fornece os seguros das aulas de campo, estágio obrigatório e práticas pedagógicas de forma satisfatória.',
                    'O programa de implementação de pós-graduação atende à comunidade acadêmica de forma satisfatória.',
                    'A disponibilidade de insumos/material para utilização em atividades de pesquisa atende à comunidade acadêmica de forma satisfatória.',
                    'Existem mecanismos institucionais de estímulo à produção acadêmica.',
                    'Os mecanismos institucionais de estímulo à produção acadêmica são satisfatórios.',
                    'Os mecanismos institucionais de estímulo à produção acadêmica são divulgados.',
                    'Há políticas para garantir o acesso satisfatório a periódicos científicos em plataformas digitais.',
                    'O programa de auxílio à participação em eventos científicos me atende de forma satisfatória.',
                    'Os programas de intercâmbio e internacionalização são divulgados.',
                    'Os programas de intercâmbio e internacionalização são satisfatórios.',
                    'Há ampla divulgação das atividades do Comitê de Ética na Pesquisa envolvendo Seres Humanos (CEP).',
                    'Há ampla divulgação das atividades do Comitê de Salva Guarda da UEAP.',
                ],
            ],

            // DIMENSÃO III
            [
                'title' => 'Dimensão III – Responsabilidade social da instituição',
                'questions' => [
                    'As atividades da UEAP contribuem para o desenvolvimento social (cultural, econômico e/ou ambiental).',
                    'As atividades da UEAP contribuem para a melhoria na qualidade de vida da comunidade de forma satisfatória.',
                    'As atividades da UEAP contribuem para a sustentabilidade ambiental de forma satisfatória.',
                    'As atividades da UEAP demonstram acessibilidade às pessoas com deficiência (PcD) de forma satisfatória.',
                    'As atividades da UEAP demonstram acessibilidade às pessoas LGBTQIAPN+ de forma satisfatória.',
                    'As atividades da UEAP fomentam a inclusão das relações étnico-raciais de forma satisfatória.',
                    'A UEAP possui parcerias com outras instituições públicas e privadas em benefício da comunidade.',
                    'As atividades da UEAP fomentam a solidariedade e o respeito pelas diferenças.',
                    'Os processos de seleção das atividades propostas pela UEAP são transparentes.',
                ],
            ],

            // DIMENSÃO IV
            [
                'title' => 'Dimensão IV – Comunicação com a sociedade',
                'questions' => [
                    'A comunicação por estes meios é eficiente.',
                    'Eu consigo me comunicar com a UEAP de forma satisfatória.',
                    'Eu conheço os campi (diferentes polos) da UEAP.',
                    'Eu conheço as formas de ingresso da UEAP.',
                    'Eu conheço os cursos de graduação da UEAP.',
                    'Eu conheço os cursos de pós-graduação da UEAP.',
                    'Eu conheço as atividades de ensino da UEAP.',
                    'Eu conheço as atividades de pesquisa da UEAP.',
                    'Eu conheço as atividades de extensão da UEAP.',
                    'As atividades abertas ao público da UEAP são acessíveis.',
                    'A agenda de atividades da UEAP abertas ao público é divulgada continuamente.',
                    'As atividades da UEAP abertas ao público são divulgadas com antecedência suficiente.',
                    'O Setor de Protocolo atende a comunidade de forma satisfatória.',
                    'O Setor de Ouvidoria atende a comunidade de forma satisfatória.',
                ],
            ],

            // DIMENSÃO V

            [
                'title' => 'Dimensão V – Políticas de pessoal e condições de trabalho',
                'questions' => [
                    'Os princípios éticos são respeitados nos ambientes que constituem a UEAP.',
                    'A relação de cordialidade entre os servidores é incentivada.',
                    'As condições de trabalho oferecidas pela instituição permitem o desempenho satisfatório das minhas funções.',
                    'As políticas de capacitação da UEAP contribuem para o meu desenvolvimento enquanto servidor.',
                    'A UEAP incentiva boas relações interpessoais no ambiente de trabalho.',
                    'As políticas de admissão de funcionários são transparentes na UEAP.',
                    'Os processos de admissão de funcionários são suficientes para a demanda de servidores no meu setor.',
                    'A Comissão Permanente de Pessoal Docente (CPPD) é eficiente.',
                    'Meu plano de carreira é adequado.',
                    'Os critérios de progressão, avaliação e reconhecimento profissional são claros e institucionalizados.',
                    'A qualificação dos servidores lotados no meu setor é adequada.',
                    'A quantidade de funcionários no meu setor é adequada para a demanda de atividades.',
                    'A relação entre quantidade de servidores da classe e carga de trabalho exigida é bem distribuída.',
                    'Os canais de comunicação institucional (SIGAA/ e-mail) atendem à comunidade acadêmica de forma satisfatória.',
                ],
            ],
            
            // DIMENSÃO VI

            [
                'title' => 'Dimensão VI – Organização e gestão da instituição',
                'questions' => [
                    'Os coordenadores de cursos cumprem de maneira satisfatória suas funções.',
                    'As chefias de setores e pró-reitorias cumprem de maneira satisfatória suas funções.',
                    'As comissões, comitês e câmaras cumprem de maneira satisfatória suas funções.',
                    'A Gestão da Reitoria da UEAP e seu gabinete cumprem de maneira satisfatória suas funções.',
                    'Há participação efetiva dos diferentes segmentos da comunidade universitária nos processos decisórios.',
                    'As tomadas de decisões na Instituição são democráticas.',
                    'As instâncias deliberativas e de gestão possuem autonomia compatível com suas atribuições institucionais.',
                    'A gestão da instituição é transparente.',
                    'As gestões internas da UEAP explicitam seus planejamentos anuais.',
                    'A atuação das representatividades no Conselho Superior (CONSU) é satisfatória.',
                    'A atuação do Colegiado do Curso é autônoma.',
                    'A atuação da Pró-Reitoria de Graduação (Prograd) é satisfatória.',
                    'A atuação da Pró-Reitoria de Pesquisa e Pós-Graduação (Propesp) é satisfatória.',
                    'A atuação da Pró-Reitoria de Extensão (Proext) é satisfatória.',
                    'A atuação da Pró-Reitoria de Planejamento e Administração (Proplad) é satisfatória.',
                ],
            ],
            
            // DIMENSÃO VII

            [
                'title' => 'Dimensão VII – Infraestrutura física e recursos de informação',
                'questions' => [
                    'A infraestrutura da biblioteca (mesas, cadeiras, espaço físico, computadores, exemplares disponíveis) atende às necessidades da comunidade acadêmica de forma satisfatória.',
                    'Os serviços prestados pela biblioteca (renovação, empréstimos, acesso a portais, atendimento, etc.) são satisfatórios.',
                    'O acervo bibliográfico é suficiente e atualizado para atender às necessidades da comunidade acadêmica.',
                    'Há equipamentos de conectividade digital adequados e suficientes para executar minhas atividades.',
                    'Existem políticas institucionais para manutenção, ampliação e melhoria da infraestrutura.',
                    'As áreas comunitárias dos campi são satisfatórias.',
                    'A higiene e a conservação das dependências da UEAP são satisfatórias.',
                    'As dependências dos campi são adequadas para atender às pessoas com deficiência ou mobilidade reduzida.',
                    'Os campi possuem acesso à internet de qualidade.',
                    'A qualidade dos equipamentos audiovisuais é adequada às necessidades dos campi.',
                    'A quantidade de equipamentos audiovisuais é adequada às necessidades dos campi.',
                    'Os campi possuem salas de aula com iluminação, conservação, climatização, dimensão, comodidade e limpeza adequadas.',
                    'Há infraestrutura para a alimentação da comunidade acadêmica nos Campi.',
                ],
            ],
            
            // DIMENSÃO VIII
            [
                'title' => 'Dimensão VIII – Planejamento e avaliação institucional',
                'questions' => [
                    'Meu setor executa autoavaliações internas.',
                    'Participo de autoavaliações executadas pelo meu setor.',
                    'A Comissão Própria de Avaliação (CPA) atua satisfatoriamente.',
                    'Os resultados das avaliações institucionais anteriores foram divulgados.',
                    'O planejamento da UEAP é colaborativo e possui representantes docentes, discentes e técnicos.',
                    'As gestões internas da UEAP incorporam as sugestões divulgadas nos relatórios de avaliação institucional no seu planejamento.',
                ],
            ],
            
            // DIMENSÃO IX

            [
                'title' => 'Dimensão IX – Políticas de atendimento aos estudantes',
                'questions' => [
                    'A recepção e socialização de ingressantes é feita de forma clara e objetiva.',
                    'O Programa de Assistência Estudantil é satisfatório.',
                    'Conheço as ações de permanência estudantil da UEAP.',
                    'As ações de permanência Estudantil são satisfatórias.',
                    'O atendimento da Unidade de Assistência Estudantil (PROEXT/DACAE/UAE) é satisfatório.',
                    'O atendimento da Coordenação de curso é satisfatório.',
                    'O atendimento da Divisão de Registro e Controle Acadêmico (DRCA) é satisfatório.',
                    'O atendimento do Setor de Estágio é satisfatório.',
                    'O atendimento da Unidade de Educação Inclusiva (UEI) é satisfatório.',
                    'A Instituição considera as avaliações e demandas estudantis na formulação de políticas institucionais.',
                    'Existe um diálogo constante entre a Instituição e as Diretorias Acadêmicas.',
                ],
            ],
            
            // DIMENSÃO X
            [
                'title' => 'Dimensão X – Sustentabilidade financeira',
                'questions' => [
                    'A política orçamentária da UEAP é transparente e coerente.',
                    'A construção do orçamento institucional é democrática e participativa, envolvendo os diferentes setores da comunidade acadêmica.',
                    'A previsão e a execução financeira direcionada para o ensino, a pesquisa e a extensão são eficazes.',
                    'O planejamento financeiro garante a continuidade das atividades institucionais.',
                    'Há disponibilidade de recursos para a expansão e crescimento da oferta institucional.',
                    'Há estratégias institucionais para captação de recursos e fortalecimento da sustentabilidade financeira.',
                ],
            ],
            


        ];

        foreach ($dimensions as $order => $data) {
            $dimension = Dimension::create([
                'survey_id' => $survey->id,
                'audience_id' => $audience->id,
                'title' => $data['title'],
                'description' => null,
                'order' => $order + 1,
            ]);

            foreach ($data['questions'] as $text) {
                Question::create([
                    'survey_id' => $survey->id,
                    'text' => $text,
                    'type' => 'likert',
                ]);
            }
        }
    }
}
