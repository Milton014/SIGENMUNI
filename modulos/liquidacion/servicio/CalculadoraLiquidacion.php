<?php

/*
|--------------------------------------------------------------------------
| CALCULADORA DE LIQUIDACIÓN
|--------------------------------------------------------------------------
|
| Esta clase contiene solamente reglas de cálculo.
|
| NO ejecuta INSERT, UPDATE ni DELETE.
| El acceso a datos se realiza mediante LiquidacionModelo.
|
*/

class CalculadoraLiquidacion
{
    private $modelo;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct($modelo)
    {
        $this->modelo = $modelo;
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULAR LIQUIDACIÓN DE UN EMPLEADO
    |--------------------------------------------------------------------------
    |
    | Retorna:
    |
    | [
    |   'detalle' => [...],
    |   'total_remunerativo' => 0,
    |   'total_descuentos' => 0,
    |   'total_no_remunerativo' => 0,
    |   'total_asignaciones' => 0,
    |   'neto' => 0,
    |   'aplica_prevision' => 1 // Solo para Gastos Protocolares
    | ]
    |
    */

    public function calcularEmpleado(
        $empleado,
        $liquidacion,
        $conceptos
    ) {
        /*
        |--------------------------------------------------------------------------
        | DATOS BÁSICOS
        |--------------------------------------------------------------------------
        */

        $empleadoId =
            (int)(
                $empleado['empleado_id']
                ?? $empleado['id']
                ?? 0
            );


        $categoriaId =
            (int)($empleado['categoria_id'] ?? 0);


        $categoriaNumero =
            (int)($empleado['categoria_codigo'] ?? 0);


        $fechaLiquidacion =
            $liquidacion['fecha_liquidacion'] ?? '';


        $tipoLiquidacion =
            strtoupper(
                trim(
                    (string)($liquidacion['tipo_liquidacion'] ?? '')
                )
            );


        /*
        |--------------------------------------------------------------------------
        | NOVEDADES DEL PERÍODO
        |--------------------------------------------------------------------------
        |
        | Se utilizan solamente en:
        |
        | MENSUAL
        | COMPLEMENTARIA
        |
        | Valores predeterminados:
        |
        | dias_liquidados    = 30
        | aplica_presentismo = 1
        |
        | AGUINALDO, COMPLEMENTARIA_SAC y GASTOS_PROTOCOLARES conservan
        | su lógica propia.
        |
        */

        $usaNovedadesPeriodo =
            in_array(
                $tipoLiquidacion,
                [
                    'MENSUAL',
                    'COMPLEMENTARIA'
                ],
                true
            );


        $diasLiquidados =
            $usaNovedadesPeriodo
                ?
                (int)(
                    $empleado['dias_liquidados']
                    ?? 30
                )
                :
                30;


        if (
            $diasLiquidados < 0
            ||
            $diasLiquidados > 30
        ) {

            throw new Exception(
                "Los días liquidados del empleado ID "
                . $empleadoId
                . " deben estar entre 0 y 30."
            );
        }


        $aplicaPresentismo =
            $usaNovedadesPeriodo
                ?
                (
                    (int)(
                        $empleado['aplica_presentismo']
                        ?? 1
                    )
                    ===
                    1
                        ? 1
                        : 0
                )
                :
                1;


        if ($diasLiquidados === 0) {

            $aplicaPresentismo = 0;
        }


        $observacionNovedad =
            $usaNovedadesPeriodo
                ?
                trim(
                    (string)(
                        $empleado['observacion_novedad']
                        ?? ''
                    )
                )
                :
                '';


        $factorDias =
            $usaNovedadesPeriodo
                ?
                (
                    $diasLiquidados
                    /
                    30
                )
                :
                1;


        /*
        |--------------------------------------------------------------------------
        | GASTOS PROTOCOLARES
        |--------------------------------------------------------------------------
        |
        | Esta modalidad tiene una rama completamente independiente.
        |
        | No se calculan sueldo básico, dedicación, suplemento, antigüedad,
        | presentismo, IASEP, Sepelio, IPS ni otros conceptos de la liquidación
        | común.
        |
        |--------------------------------------------------------------------------
        */

        if (
            $tipoLiquidacion
            ===
            'GASTOS_PROTOCOLARES'
        ) {

            return $this->calcularGastosProtocolares(
                $empleado,
                $conceptos
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TIPO SAC
        |--------------------------------------------------------------------------
        */

        $esSAC =
            in_array(
                $tipoLiquidacion,
                [
                    'SAC',
                    'AGUINALDO',
                    'COMPLEMENTARIA_SAC'
                ],
                true
            );


        /*
        |--------------------------------------------------------------------------
        | ANTIGÜEDAD EFECTIVA
        |--------------------------------------------------------------------------
        |
        | Se calcula contra la FECHA DE LIQUIDACIÓN utilizando exclusivamente
        | empleado_periodo_laboral.
        |
        | Los períodos en que el empleado estuvo inactivo NO generan antigüedad.
        |
        | De esta forma una reincorporación conserva el servicio anterior, pero
        | no suma el tiempo transcurrido fuera de la relación laboral.
        |
        */

        $antiguedadAnios =
            $this->modelo
                ->calcularAniosAntiguedadEfectiva(
                    $empleadoId,
                    $fechaLiquidacion
                );


        /*
        |--------------------------------------------------------------------------
        | VALORES VIGENTES DESDE CONCEPTO_VALOR
        |--------------------------------------------------------------------------
        |
        | En la nueva arquitectura, los únicos importes generales administrados
        | desde Gestión de Conceptos son:
        |
        | 101 - Sueldo Básico
        | 102 - Dedicación Funcional
        | 104 - Suplemento Especial
        |
        | Estos tres valores se obtienen por categoría y vigencia desde
        | concepto_valor.
        |
        | Los demás importes o porcentajes particulares se cargan desde
        | Conceptos por Empleado.
        |
        |--------------------------------------------------------------------------
        */

        $valorBasico =
            $this->modelo
                ->obtenerValorConceptoVigente(
                    '101',
                    $categoriaId,
                    $fechaLiquidacion
                );


        $valorDedicacion =
            $this->modelo
                ->obtenerValorConceptoVigente(
                    '102',
                    $categoriaId,
                    $fechaLiquidacion
                );


        $valorSuplemento =
            $this->modelo
                ->obtenerValorConceptoVigente(
                    '104',
                    $categoriaId,
                    $fechaLiquidacion
                );


        $sueldoBasico =
            $valorBasico !== null
                ?
                (float)$valorBasico['monto']
                :
                0;


        $dedicacion =
            $valorDedicacion !== null
                ?
                (float)$valorDedicacion['monto']
                :
                0;


        $suplemento =
            $valorSuplemento !== null
                ?
                (float)$valorSuplemento['monto']
                :
                0;


        /*
        |--------------------------------------------------------------------------
        | DEDICACIÓN FUNCIONAL - FALLBACK HISTÓRICO
        |--------------------------------------------------------------------------
        |
        | Se conserva la regla histórica de SIGENMUNI:
        |
        | si no existe un valor vigente para 102 y el sueldo básico es mayor a
        | cero, la dedicación toma el mismo importe del básico.
        |
        | Si 102 tiene un valor vigente explícito de 0.00, ese cero se respeta.
        |
        */

        if (
            $valorDedicacion === null
            &&
            $sueldoBasico > 0
        ) {

            $dedicacion =
                $sueldoBasico;
        }


        /*
        |--------------------------------------------------------------------------
        | PRORRATEO POR DÍAS LIQUIDADOS
        |--------------------------------------------------------------------------
        |
        | Los valores obtenidos desde concepto_valor representan el mes completo
        | de 30 días.
        |
        | Para MENSUAL y COMPLEMENTARIA:
        |
        | importe_liquidado = importe_mensual * dias_liquidados / 30
        |
        | Se prorratean:
        |
        | 101 - Sueldo Básico
        | 102 - Dedicación Funcional
        | 104 - Suplemento Especial
        |
        | Los conceptos porcentuales que usan BASICO o BASICO_MAS_DEDICACION
        | tomarán automáticamente estos importes ya prorrateados.
        |
        */

        $sueldoBasicoMensual =
            $sueldoBasico;

        $dedicacionMensual =
            $dedicacion;

        $suplementoMensual =
            $suplemento;


        if ($usaNovedadesPeriodo) {

            $sueldoBasico =
                round(
                    $sueldoBasicoMensual
                    *
                    $factorDias,
                    2
                );


            $dedicacion =
                round(
                    $dedicacionMensual
                    *
                    $factorDias,
                    2
                );


            $suplemento =
                round(
                    $suplementoMensual
                    *
                    $factorDias,
                    2
                );
        }

        /*
        |--------------------------------------------------------------------------
        | ADICIONAL FUNCIÓN JERÁRQUICA - 103
        |--------------------------------------------------------------------------
        |
        | Ya no se calcula con un porcentaje fijo ni depende de la categoría.
        |
        | En la nueva arquitectura:
        |
        | - el concepto 103 debe estar configurado como PORCENTAJE;
        | - el porcentaje se carga desde Conceptos por Empleado;
        | - base_calculo = BASICO_MAS_DEDICACION;
        | - si el empleado no tiene asignado el concepto 103, no se liquida.
        |
        | El cálculo se realiza en el recorrido de conceptos particulares.
        |
        */




        /*
        |--------------------------------------------------------------------------
        | ANTIGÜEDAD - 108
        |--------------------------------------------------------------------------
        |
        | 2% por año sobre sueldo básico.
        |
        | Cuando hay novedad de días, utiliza el básico ya prorrateado.
        |
        */

        $antiguedad =
            (
                $sueldoBasico
                *
                0.02
            )
            *
            $antiguedadAnios;


        /*
        |--------------------------------------------------------------------------
        | PRESENTISMO - 109
        |--------------------------------------------------------------------------
        |
        | Si aplica_presentismo = 1:
        |
        | 15% sobre:
        |
        | básico + dedicación + suplemento
        |
        | Los tres importes ya se encuentran prorrateados por días cuando
        | corresponde.
        |
        | Si aplica_presentismo = 0:
        |
        | el concepto 109 no se liquida.
        |
        */

        $presentismo =
            $aplicaPresentismo === 1
                ?
                round(
                    (
                        $sueldoBasico
                        +
                        $dedicacion
                        +
                        $suplemento
                    )
                    *
                    0.15,
                    2
                )
                :
                0;


        /*
        |--------------------------------------------------------------------------
        | CONCEPTOS DEL EMPLEADO
        |--------------------------------------------------------------------------
        */

        $conceptosEmpleado =
            $this->modelo
                ->obtenerConceptosEmpleadoVigentes(
                    $empleadoId,
                    $fechaLiquidacion
                );


        /*
        |--------------------------------------------------------------------------
        | REMUNERATIVOS BASE
        |--------------------------------------------------------------------------
        */

        $detalleRemunerativosBase = [];


        $observacionDias =
            $usaNovedadesPeriodo
            &&
            $diasLiquidados !== 30
                ?
                ' - '
                . $diasLiquidados
                . '/30 días'
                :
                '';


        $observacionExtraNovedad =
            $observacionNovedad !== ''
                ?
                ' - '
                . $observacionNovedad
                :
                '';


        $this->agregarDetalle(
            $detalleRemunerativosBase,
            '101',
            $sueldoBasico,
            1,
            0,
            0,
            'Sueldo básico'
            . $observacionDias
            . $observacionExtraNovedad
        );


        $this->agregarDetalle(
            $detalleRemunerativosBase,
            '102',
            $dedicacion,
            1,
            0,
            0,
            'Dedicación funcional'
            . $observacionDias
        );


        $this->agregarDetalle(
            $detalleRemunerativosBase,
            '104',
            $suplemento,
            1,
            0,
            0,
            'Suplemento especial'
            . $observacionDias
        );


        $this->agregarDetalle(
            $detalleRemunerativosBase,
            '108',
            $antiguedad,
            $antiguedadAnios,
            $antiguedadAnios * 2,
            0,
            '2% por año de antigüedad efectiva'
        );


        if ($aplicaPresentismo === 1) {

            $this->agregarDetalle(
                $detalleRemunerativosBase,
                '109',
                $presentismo,
                1,
                15,
                0,
                '15% sobre básico + dedicación + suplemento'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CONCEPTOS PARTICULARES DEL EMPLEADO
        |--------------------------------------------------------------------------
        |
        | En la nueva arquitectura:
        |
        | MANUAL      -> monto_manual
        | PORCENTAJE  -> porcentaje_manual + base_calculo
        |
        | No se utilizan monto_fijo ni porcentaje general del concepto.
        |
        | Para los conceptos porcentuales, base_calculo determina la base
        | sobre la que se aplica el porcentaje individual.
        |
        | En este primer recorrido se incorporan solamente conceptos
        | REMUNERATIVOS y NO_REMUNERATIVOS.
        |
        | Las asignaciones familiares y descuentos se procesan después.
        |
        |--------------------------------------------------------------------------
        */

        foreach (
            $conceptosEmpleado
            as
            $conceptoEmpleado
        ) {

            $codigo =
                (string)(
                    $conceptoEmpleado['codigo']
                    ?? ''
                );


            if ($codigo === '') {

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | ASIGNACIONES 201-209
            |--------------------------------------------------------------------------
            */

            if (
                (int)$codigo >= 201
                &&
                (int)$codigo <= 209
            ) {

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | DESCUENTOS
            |--------------------------------------------------------------------------
            */

            if (
                (int)$codigo >= 301
                &&
                (int)$codigo <= 399
            ) {

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | APORTES PATRONALES
            |--------------------------------------------------------------------------
            */

            if (
                (int)$codigo >= 401
                &&
                (int)$codigo <= 499
            ) {

                continue;
            }


            $categoriaConcepto =
                strtoupper(
                    trim(
                        (string)(
                            $conceptoEmpleado[
                                'concepto_categoria'
                            ]
                            ?? ''
                        )
                    )
                );


            if (
                $categoriaConcepto !== 'REMUNERATIVO'
                &&
                $categoriaConcepto !== 'NO_REMUNERATIVO'
            ) {

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | EVITAR DUPLICAR CONCEPTOS AUTOMÁTICOS / POR CATEGORÍA
            |--------------------------------------------------------------------------
            */

            if (
                in_array(
                    $codigo,
                    [
                        '101',
                        '102',
                        '104',
                        '108',
                        '109'
                    ],
                    true
                )
            ) {

                continue;
            }


            $formaCalculo =
                strtoupper(
                    trim(
                        (string)(
                            $conceptoEmpleado['forma_calculo']
                            ?? ''
                        )
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | COMPATIBILIDAD HISTÓRICA
            |--------------------------------------------------------------------------
            */

            if ($formaCalculo === 'FIJO') {

                $formaCalculo =
                    'MANUAL';
            }


            $montoManual =
                round(
                    (float)(
                        $conceptoEmpleado['monto_manual']
                        ?? 0
                    ),
                    2
                );


            $porcentajeManual =
                (float)(
                    $conceptoEmpleado['porcentaje_manual']
                    ?? 0
                );


            $cantidad =
                (float)(
                    $conceptoEmpleado['cantidad']
                    ?? 1
                );


            if ($cantidad <= 0) {

                $cantidad = 1;
            }


            $observacion =
                !empty(
                    $conceptoEmpleado['observacion']
                )
                    ?
                    $conceptoEmpleado['observacion']
                    :
                    (
                        !empty(
                            $conceptoEmpleado['concepto_nombre']
                        )
                            ?
                            $conceptoEmpleado['concepto_nombre']
                            :
                            'Concepto asignado al empleado'
                    );


            /*
            |--------------------------------------------------------------------------
            | MANUAL
            |--------------------------------------------------------------------------
            */

            if (
                $formaCalculo === 'MANUAL'
                &&
                $montoManual > 0
            ) {

                $this->agregarDetalle(
                    $detalleRemunerativosBase,
                    $codigo,
                    $montoManual,
                    $cantidad,
                    0,
                    1,
                    $observacion
                );


                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | PORCENTAJE
            |--------------------------------------------------------------------------
            |
            | El porcentaje individual se carga desde Conceptos por Empleado.
            |
            | La base se toma de base_calculo configurada en Gestión de Conceptos.
            |
            | Bases disponibles en este punto del cálculo:
            |
            | BASICO
            |     -> 101 Sueldo Básico
            |
            | BASICO_MAS_DEDICACION
            |     -> 101 Sueldo Básico + 102 Dedicación Funcional
            |
            | Compatibilidad histórica:
            |
            | 103 sin base -> BASICO_MAS_DEDICACION
            | 105 sin base -> BASICO
            | 110 sin base -> BASICO
            |
            | TOTAL_REMUNERATIVO no se admite aquí para conceptos remunerativos,
            | porque el total remunerativo todavía se está construyendo.
            |
            */

            if (
                $formaCalculo === 'PORCENTAJE'
                &&
                $porcentajeManual > 0
            ) {

                $baseCalculo =
                    strtoupper(
                        trim(
                            (string)(
                                $conceptoEmpleado['base_calculo']
                                ?? ''
                            )
                        )
                    );


                /*
                |--------------------------------------------------------------------------
                | COMPATIBILIDAD 103 / 105 / 110
                |--------------------------------------------------------------------------
                */

                if ($baseCalculo === '') {

                    if ($codigo === '103') {

                        $baseCalculo =
                            'BASICO_MAS_DEDICACION';

                    } elseif (
                        $codigo === '105'
                        ||
                        $codigo === '110'
                    ) {

                        $baseCalculo =
                            'BASICO';
                    }
                }


                $basesPorcentajeConceptos = [

                    'BASICO' =>
                        $sueldoBasico,

                    'BASICO_MAS_DEDICACION' =>
                        $sueldoBasico
                        +
                        $dedicacion
                ];


                if (
                    !array_key_exists(
                        $baseCalculo,
                        $basesPorcentajeConceptos
                    )
                ) {

                    $nombreConcepto =
                        !empty(
                            $conceptoEmpleado['concepto_nombre']
                        )
                            ?
                            $conceptoEmpleado['concepto_nombre']
                            :
                            'Concepto sin nombre';


                    throw new Exception(
                        "El concepto porcentual "
                        . $codigo
                        . " - "
                        . $nombreConcepto
                        . " no tiene una base compatible configurada. "
                        . "Para conceptos remunerativos use BASICO o BASICO_MAS_DEDICACION."
                    );
                }


                $basePorcentaje =
                    (float)$basesPorcentajeConceptos[
                        $baseCalculo
                    ];


                if ($basePorcentaje < 0) {

                    $basePorcentaje = 0;
                }


                $montoCalculado =
                    round(
                        $basePorcentaje
                        *
                        (
                            $porcentajeManual
                            /
                            100
                        ),
                        2
                    );


                $this->agregarDetalle(
                    $detalleRemunerativosBase,
                    $codigo,
                    $montoCalculado,
                    $cantidad,
                    $porcentajeManual,
                    1,
                    $observacion
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | BASE SAC
        |--------------------------------------------------------------------------
        |
        | Se construye con los conceptos remunerativos que participan del SAC.
        |
        | En AGUINALDO y COMPLEMENTARIA_SAC esta base representa el importe
        | mensual remunerativo de referencia. El prorrateo por días del semestre
        | se realiza dentro de calcularSAC().
        |
        */

        $baseSac = 0;


        foreach (
            $detalleRemunerativosBase
            as
            $item
        ) {

            $codigo =
                (string)$item['codigo'];


            if ($codigo === '112') {

                continue;
            }


            if (
                isset($conceptos[$codigo]) &&
                strtoupper(
                    trim(
                        (string)$conceptos[$codigo]['categoria']
                    )
                )
                ===
                'REMUNERATIVO'
            ) {

                /*
                |--------------------------------------------------------------------------
                | aplica_sac
                |--------------------------------------------------------------------------
                |
                | Si el concepto tiene configurado aplica_sac,
                | respetamos esa definición.
                |
                | Para conceptos históricos donde el campo pudiera estar en 0,
                | los conceptos salariales base siguen participando.
                |
                */

                $aplicaSac =
                    (int)(
                        $conceptos[$codigo]['aplica_sac']
                        ?? 0
                    );


                $conceptosBaseSac = [
                    '101',
                    '102',
                    '103',
                    '104',
                    '108',
                    '109'
                ];


                if (
                    $aplicaSac === 1 ||
                    in_array(
                        $codigo,
                        $conceptosBaseSac,
                        true
                    )
                ) {

                    $baseSac +=
                        (float)$item['monto'];
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | AGUINALDO / COMPLEMENTARIA DE SAC
        |--------------------------------------------------------------------------
        */

        if ($esSAC) {

            return $this->calcularSAC(
                $empleado,
                $conceptosEmpleado,
                $conceptos,
                $categoriaNumero,
                $baseSac,
                $tipoLiquidacion
            );
        }


        /*
        |--------------------------------------------------------------------------
        | LIQUIDACIÓN MENSUAL / AJUSTE / COMPLEMENTARIA
        |--------------------------------------------------------------------------
        */

        return $this->calcularLiquidacionComun(
            $empleado,
            $conceptosEmpleado,
            $conceptos,
            $detalleRemunerativosBase,
            $categoriaNumero,
            $sueldoBasico,
            $dedicacion,
            $suplemento
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULAR GASTOS PROTOCOLARES
    |--------------------------------------------------------------------------
    |
    | Datos esperados desde liquidacion_protocolar:
    |
    | - empleado_id
    | - importe
    | - aplica_prevision
    |
    | Regla:
    |
    | Con aportes:
    |   113 - Gastos Protocolares
    |   301 - Caja de Previsión Social 11%
    |   401 - Aporte Patronal Caja de Previsión Social 16%
    |
    | Jubilado / Exento:
    |   113 - Gastos Protocolares
    |   sin 301
    |   sin 401
    |
    |--------------------------------------------------------------------------
    */

    private function calcularGastosProtocolares(
        $empleado,
        $conceptos
    ) {
        /*
        |--------------------------------------------------------------------------
        | VALIDAR CONCEPTOS NECESARIOS
        |--------------------------------------------------------------------------
        */

        foreach (
            [
                '113',
                '301',
                '401'
            ]
            as
            $codigoObligatorio
        ) {

            if (
                !isset(
                    $conceptos[$codigoObligatorio]
                )
            ) {

                throw new Exception(
                    "No se puede calcular Gastos Protocolares porque falta o está inactivo el concepto "
                    . $codigoObligatorio
                    . "."
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | DATOS DE LA CARGA
        |--------------------------------------------------------------------------
        */

        $empleadoId =
            (int)(
                $empleado['empleado_id']
                ?? $empleado['id']
                ?? 0
            );


        if ($empleadoId <= 0) {

            throw new Exception(
                "El empleado cargado en Gastos Protocolares no es válido."
            );
        }


        $importe =
            round(
                (float)(
                    $empleado['importe']
                    ?? 0
                ),
                2
            );


        if ($importe <= 0) {

            throw new Exception(
                "El importe de Gastos Protocolares debe ser mayor a cero para el empleado ID "
                . $empleadoId
                . "."
            );
        }


        $aplicaPrevision =
            (int)(
                $empleado['aplica_prevision']
                ?? 1
            )
            ===
            1
                ? 1
                : 0;


        /*
        |--------------------------------------------------------------------------
        | DETALLE
        |--------------------------------------------------------------------------
        */

        $detalleItems = [];


        $this->agregarDetalle(
            $detalleItems,
            '113',
            $importe,
            1,
            0,
            1,
            'Importe de Gastos Protocolares cargado manualmente'
        );


        /*
        |--------------------------------------------------------------------------
        | PREVISIÓN SOCIAL
        |--------------------------------------------------------------------------
        */

        $descuentoCaja = 0;
        $aportePatronalCaja = 0;


        if ($aplicaPrevision === 1) {

            $descuentoCaja =
                round(
                    $importe
                    *
                    0.11,
                    2
                );


            $aportePatronalCaja =
                round(
                    $importe
                    *
                    0.16,
                    2
                );


            $this->agregarDetalle(
                $detalleItems,
                '301',
                $descuentoCaja,
                1,
                11,
                0,
                '11% Caja de Previsión Social sobre Gastos Protocolares'
            );


            $this->agregarDetalle(
                $detalleItems,
                '401',
                $aportePatronalCaja,
                1,
                16,
                0,
                '16% Aporte Patronal Caja de Previsión Social sobre Gastos Protocolares'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */

        $totalRemunerativo =
            $importe;


        $totalDescuentos =
            $descuentoCaja;


        $totalNoRemunerativo =
            0;


        $totalAsignaciones =
            0;


        $neto =
            $totalRemunerativo
            -
            $totalDescuentos;


        return [

            'detalle' =>
                $detalleItems,

            'total_remunerativo' =>
                round(
                    $totalRemunerativo,
                    2
                ),

            'total_descuentos' =>
                round(
                    $totalDescuentos,
                    2
                ),

            'total_no_remunerativo' =>
                $totalNoRemunerativo,

            'total_asignaciones' =>
                $totalAsignaciones,

            'neto' =>
                round(
                    $neto,
                    2
                ),

            'aplica_prevision' =>
                $aplicaPrevision
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | LIQUIDACIÓN COMÚN
    |--------------------------------------------------------------------------
    |
    | Se utiliza para:
    |
    | MENSUAL
    | AJUSTE
    | COMPLEMENTARIA
    |
    | Por ahora conservan la misma mecánica de cálculo.
    |
    */

    private function calcularLiquidacionComun(
        $empleado,
        $conceptosEmpleado,
        $conceptos,
        $detalleRemunerativosBase,
        $categoriaNumero,
        $sueldoBasico,
        $dedicacion,
        $suplemento
    ) {
        $detalleItems =
            $detalleRemunerativosBase;


        /*
        |--------------------------------------------------------------------------
        | TOTAL REMUNERATIVO
        |--------------------------------------------------------------------------
        */

        $totalRemunerativo = 0;


        foreach (
            $detalleItems
            as
            $item
        ) {

            $codigo =
                (string)$item['codigo'];


            if (
                isset($conceptos[$codigo]) &&
                strtoupper(
                    trim(
                        (string)$conceptos[$codigo]['categoria']
                    )
                )
                ===
                'REMUNERATIVO'
            ) {

                $totalRemunerativo +=
                    (float)$item['monto'];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ASIGNACIONES FAMILIARES 201-209
        |--------------------------------------------------------------------------
        |
        | Nueva arquitectura:
        |
        | - se administran únicamente desde Conceptos por Empleado;
        | - monto_manual = importe por unidad;
        | - cantidad = cantidad correspondiente al empleado;
        | - no se utilizan estructuras salariales heredadas ni monto_fijo.
        |
        |--------------------------------------------------------------------------
        */

        foreach (
            $conceptosEmpleado
            as
            $conceptoEmpleado
        ) {

            $codigo =
                (string)(
                    $conceptoEmpleado['codigo']
                    ?? ''
                );


            $codigoNumero =
                (int)$codigo;


            if (
                $codigoNumero < 201
                ||
                $codigoNumero > 209
            ) {

                continue;
            }


            $categoriaConcepto =
                strtoupper(
                    trim(
                        (string)(
                            $conceptoEmpleado[
                                'concepto_categoria'
                            ]
                            ?? ''
                        )
                    )
                );


            if (
                $categoriaConcepto
                !==
                'ASIGNACION_FAMILIAR'
            ) {

                continue;
            }


            $montoUnitario =
                round(
                    (float)(
                        $conceptoEmpleado['monto_manual']
                        ?? 0
                    ),
                    2
                );


            if ($montoUnitario <= 0) {

                continue;
            }


            $cantidad =
                (float)(
                    $conceptoEmpleado['cantidad']
                    ?? 1
                );


            if ($cantidad <= 0) {

                $cantidad = 1;
            }


            $montoTotal =
                round(
                    $montoUnitario
                    *
                    $cantidad,
                    2
                );


            $observacion =
                !empty(
                    $conceptoEmpleado['observacion']
                )
                    ?
                    $conceptoEmpleado['observacion']
                    :
                    (
                        !empty(
                            $conceptoEmpleado['concepto_nombre']
                        )
                            ?
                            $conceptoEmpleado['concepto_nombre']
                            :
                            'Asignación familiar'
                    );


            $this->agregarDetalle(
                $detalleItems,
                $codigo,
                $montoTotal,
                $cantidad,
                0,
                1,
                $observacion
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BASES DE CÁLCULO
        |--------------------------------------------------------------------------
        */

        $basesCalculo = [

            'TOTAL_REMUNERATIVO' =>
                $totalRemunerativo,

            'BASICO' =>
                $sueldoBasico,

            'BASICO_MAS_DEDICACION' =>
                $sueldoBasico
                +
                $dedicacion,

            'BASICO_MAS_DEDICACION_MAS_SUPLEMENTO' =>
                $sueldoBasico
                +
                $dedicacion
                +
                $suplemento
        ];


        /*
        |--------------------------------------------------------------------------
        | DESCUENTOS PERSONALES
        |--------------------------------------------------------------------------
        */

        $caja =
            round(
                $totalRemunerativo
                *
                0.11,
                2
            );


        $obraSocial =
            round(
                $totalRemunerativo
                *
                0.05,
                2
            );


        $sepelio =
            round(
                $totalRemunerativo
                *
                0.01,
                2
            );


        $voluntario =
            round(
                $totalRemunerativo
                *
                0.08,
                2
            );


        $ips1 =
            (
                $categoriaNumero <= 21
            )
                ?
                round(
                    $totalRemunerativo
                    *
                    0.01,
                    2
                )
                :
                0;


        $ips2 =
            (
                $categoriaNumero <= 22
            )
                ?
                round(
                    $totalRemunerativo
                    *
                    0.02,
                    2
                )
                :
                0;


        /*
        |--------------------------------------------------------------------------
        | DESCUENTOS OBLIGATORIOS Y BASE PORCENTUAL ESPECIAL
        |--------------------------------------------------------------------------
        |
        | Descuentos obligatorios que reducen la base de los embargos:
        |
        | 301, 302, 303, 304, 306 y 307
        |
        | IMPORTANTE:
        | 309 NO forma parte de esta base.
        |
        | TOTAL_REMUNERATIVO
        |     -> se calcula sobre el total remunerativo.
        |
        | TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS
        |     -> se calcula sobre:
        |
        |        Total Remunerativo
        |        - 301
        |        - 302
        |        - 303
        |        - 304
        |        - 306
        |        - 307
        |
        | Así Gremios, Embargos y futuros descuentos porcentuales quedan
        | parametrizados por base_calculo, sin depender de códigos fijos.
        |
        */

        $totalDescuentosObligatorios =
            $caja
            +
            $obraSocial
            +
            $sepelio
            +
            $voluntario
            +
            $ips1
            +
            $ips2;


        $baseRemunerativoMenosObligatorios =
            $totalRemunerativo
            -
            $totalDescuentosObligatorios;


        if ($baseRemunerativoMenosObligatorios < 0) {

            $baseRemunerativoMenosObligatorios = 0;
        }


        $basesCalculo[
            'TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS'
        ] =
            $baseRemunerativoMenosObligatorios;


        /*
        |--------------------------------------------------------------------------
        | DETALLE DE DESCUENTOS
        |--------------------------------------------------------------------------
        */

        $this->agregarDetalle(
            $detalleItems,
            '301',
            $caja,
            1,
            11,
            0,
            '11% Caja de Previsión Social'
        );


        $this->agregarDetalle(
            $detalleItems,
            '302',
            $obraSocial,
            1,
            5,
            0,
            '5% IASEP Obra Social'
        );


        $this->agregarDetalle(
            $detalleItems,
            '303',
            $sepelio,
            1,
            1,
            0,
            '1% IASEP Sepelio'
        );


        $this->agregarDetalle(
            $detalleItems,
            '304',
            $voluntario,
            1,
            8,
            0,
            '8% IASEP Voluntario'
        );


        $this->agregarDetalle(
            $detalleItems,
            '306',
            $ips1,
            1,
            1,
            0,
            '1% IPS para categoría 21 o inferior'
        );


        $this->agregarDetalle(
            $detalleItems,
            '307',
            $ips2,
            1,
            2,
            0,
            '2% IPS para categoría 22 o inferior'
        );


        /*
        |--------------------------------------------------------------------------
        | DESCUENTOS ASIGNADOS AL EMPLEADO
        |--------------------------------------------------------------------------
        |
        | Todos los descuentos particulares se procesan de forma parametrizable.
        |
        | MANUAL:
        |     usa monto_manual.
        |
        | PORCENTAJE:
        |     usa porcentaje_manual y base_calculo.
        |
        | Ejemplos:
        |
        | Gremio:
        |     base = TOTAL_REMUNERATIVO
        |
        | Cualquier Embargo:
        |     base =
        |     TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS
        |
        | 309 es un descuento normal, pero NO integra los descuentos
        | obligatorios que reducen la base de los embargos.
        |
        | Solo se excluyen los descuentos automáticos:
        | 301, 302, 303, 304, 306 y 307.
        |
        */

        $this->agregarDescuentosAsignadosEmpleado(
            $detalleItems,
            $conceptosEmpleado,
            $basesCalculo,
            [
                '301',
                '302',
                '303',
                '304',
                '306',
                '307'
            ],
            false
        );


        /*
        |--------------------------------------------------------------------------
        | APORTES PATRONALES
        |--------------------------------------------------------------------------
        */

        $patronalCaja =
            round(
                $totalRemunerativo
                *
                0.16,
                2
            );


        $patronalObra =
            round(
                $totalRemunerativo
                *
                0.04,
                2
            );


        $patronalIps =
            round(
                $totalRemunerativo
                *
                0.02,
                2
            );


        $this->agregarDetalle(
            $detalleItems,
            '401',
            $patronalCaja,
            1,
            16,
            0,
            '16% Caja patronal'
        );


        $this->agregarDetalle(
            $detalleItems,
            '402',
            $patronalObra,
            1,
            4,
            0,
            '4% Obra social patronal'
        );


        $this->agregarDetalle(
            $detalleItems,
            '403',
            $patronalIps,
            1,
            2,
            0,
            '2% IPS patronal'
        );


        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */

        $totalNoRemunerativo = 0;

        $totalAsignaciones = 0;

        $totalDescuentos = 0;


        foreach (
            $detalleItems
            as
            $item
        ) {

            $codigo =
                (string)$item['codigo'];


            $monto =
                (float)$item['monto'];


            /*
            |--------------------------------------------------------------------------
            | NO REMUNERATIVOS
            |--------------------------------------------------------------------------
            */

            if (
                isset($conceptos[$codigo]) &&
                strtoupper(
                    trim(
                        (string)$conceptos[$codigo]['categoria']
                    )
                )
                ===
                'NO_REMUNERATIVO'
            ) {

                $totalNoRemunerativo +=
                    $monto;
            }


            /*
            |--------------------------------------------------------------------------
            | ASIGNACIONES
            |--------------------------------------------------------------------------
            */

            if (
                (int)$codigo >= 201 &&
                (int)$codigo <= 209
            ) {

                $totalAsignaciones +=
                    $monto;
            }


            /*
            |--------------------------------------------------------------------------
            | DESCUENTOS
            |--------------------------------------------------------------------------
            */

            if (
                (int)$codigo >= 301 &&
                (int)$codigo <= 399
            ) {

                $totalDescuentos +=
                    $monto;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | NETO
        |--------------------------------------------------------------------------
        */

        $neto =
            $totalRemunerativo
            -
            $totalDescuentos
            +
            $totalNoRemunerativo
            +
            $totalAsignaciones;


        return [

            'detalle' =>
                $detalleItems,

            'total_remunerativo' =>
                round(
                    $totalRemunerativo,
                    2
                ),

            'total_descuentos' =>
                round(
                    $totalDescuentos,
                    2
                ),

            'total_no_remunerativo' =>
                round(
                    $totalNoRemunerativo,
                    2
                ),

            'total_asignaciones' =>
                round(
                    $totalAsignaciones,
                    2
                ),

            'neto' =>
                round(
                    $neto,
                    2
                )
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULAR AGUINALDO
    |--------------------------------------------------------------------------
    */

    private function calcularSAC(
        $empleado,
        $conceptosEmpleado,
        $conceptos,
        $categoriaNumero,
        $baseSac,
        $tipoLiquidacion = 'AGUINALDO'
    ) {
        $detalleItems = [];


        /*
        |--------------------------------------------------------------------------
        | DÍAS COMPUTABLES DE SAC
        |--------------------------------------------------------------------------
        |
        | 180 días = SAC completo del semestre.
        | 1 a 179  = SAC proporcional.
        | 0        = no genera SAC.
        |
        | El valor llega desde liquidacion_novedad.dias_sac.
        | Si no existe una novedad específica, se utilizan 180 días.
        |
        */

        $diasSac =
            (int)(
                $empleado['dias_sac']
                ?? 180
            );


        if (
            $diasSac < 0
            ||
            $diasSac > 180
        ) {

            $empleadoId =
                (int)(
                    $empleado['empleado_id']
                    ?? $empleado['id']
                    ?? 0
                );


            throw new Exception(
                "Los días de SAC del empleado ID "
                . $empleadoId
                . " deben estar entre 0 y 180."
            );
        }


        $factorSac =
            $diasSac
            /
            180;


        /*
        |--------------------------------------------------------------------------
        | SAC BRUTO
        |--------------------------------------------------------------------------
        |
        | Fórmula:
        |
        | SAC = (Base Remunerativa × 50% / 180) × días_sac
        |
        | Equivalente a:
        |
        | SAC = Base Remunerativa × 0.50 × (días_sac / 180)
        |
        | Ejemplo:
        |
        | Base = 90.000
        | 90.000 × 50% = 45.000
        | 45.000 / 180 = 250
        | 250 × 90 días = 22.500
        |
        */

        $sacBruto =
            round(
                $baseSac
                *
                0.50
                *
                $factorSac,
                2
            );


        /*
        |--------------------------------------------------------------------------
        | PORCENTAJE EFECTIVO DEL SAC
        |--------------------------------------------------------------------------
        |
        | 180 días -> 50%
        | 90 días  -> 25%
        | 60 días  -> 16,6667%
        |
        */

        $porcentajeSacAplicado =
            round(
                50
                *
                $factorSac,
                4
            );


        /*
        |--------------------------------------------------------------------------
        | CONCEPTO 150 - SAC
        |--------------------------------------------------------------------------
        */

        $tipoLiquidacionSac =
            strtoupper(
                trim(
                    (string)$tipoLiquidacion
                )
            );


        $prefijoObservacionSac =
            $tipoLiquidacionSac === 'COMPLEMENTARIA_SAC'
                ? 'Complementaria de SAC'
                : 'Sueldo Anual Complementario';


        $observacionSac =
            $diasSac === 180
                ?
                $prefijoObservacionSac
                . ' - 50% de conceptos remunerativos - 180/180 días'
                :
                (
                    $prefijoObservacionSac
                    . ' proporcional - '
                    . $diasSac
                    . '/180 días sobre conceptos remunerativos'
                );


        $this->agregarDetalle(
            $detalleItems,
            '150',
            $sacBruto,
            1,
            $porcentajeSacAplicado,
            0,
            $observacionSac
        );


        /*
        |--------------------------------------------------------------------------
        | DESCUENTOS SOBRE SAC
        |--------------------------------------------------------------------------
        |
        | Todos los descuentos se calculan sobre el SAC bruto resultante.
        | Por lo tanto, cuando el SAC es proporcional, los descuentos también
        | quedan automáticamente proporcionados.
        |
        */

        $cajaSac =
            round(
                $sacBruto
                *
                0.11,
                2
            );


        $ips1Sac =
            (
                $categoriaNumero <= 21
            )
                ?
                round(
                    $sacBruto
                    *
                    0.01,
                    2
                )
                :
                0;


        $ips2Sac =
            (
                $categoriaNumero <= 22
            )
                ?
                round(
                    $sacBruto
                    *
                    0.02,
                    2
                )
                :
                0;


        /*
        |--------------------------------------------------------------------------
        | BASE PORCENTUAL ESPECIAL EN SAC
        |--------------------------------------------------------------------------
        |
        | En SAC, los descuentos obligatorios automáticos que actualmente
        | se calculan son:
        |
        | 301 - Caja
        | 306 - IPS 1%
        | 307 - IPS 2%
        |
        | Por eso, para un descuento configurado con:
        |
        | TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS
        |
        | la base será:
        |
        | SAC Bruto - 301 - 306 - 307
        |
        | Solo se procesan conceptos particulares con aplica_sac = 1.
        |
        */

        $totalDescuentosObligatoriosSac =
            $cajaSac
            +
            $ips1Sac
            +
            $ips2Sac;


        $baseSacMenosObligatorios =
            $sacBruto
            -
            $totalDescuentosObligatoriosSac;


        if ($baseSacMenosObligatorios < 0) {

            $baseSacMenosObligatorios = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | DETALLE SAC
        |--------------------------------------------------------------------------
        */

        $this->agregarDetalle(
            $detalleItems,
            '301',
            $cajaSac,
            1,
            11,
            0,
            '11% Caja de Previsión Social sobre aguinaldo'
        );


        $this->agregarDetalle(
            $detalleItems,
            '306',
            $ips1Sac,
            1,
            1,
            0,
            '1% IPS sobre aguinaldo'
        );


        $this->agregarDetalle(
            $detalleItems,
            '307',
            $ips2Sac,
            1,
            2,
            0,
            '2% IPS sobre aguinaldo'
        );


        /*
        |--------------------------------------------------------------------------
        | APORTES PATRONALES SOBRE SAC
        |--------------------------------------------------------------------------
        |
        | En AGUINALDO y COMPLEMENTARIA_SAC corresponden únicamente:
        |
        | 401 - Caja de Previsión Social Patronal 16%
        | 403 - IPS Patronal 2%
        |
        | No corresponde el concepto 402 en SAC.
        |
        | Ambos se calculan sobre el SAC bruto ya proporcionado por días.
        |
        */

        $patronalCajaSac =
            round(
                $sacBruto
                *
                0.16,
                2
            );


        $patronalIpsSac =
            round(
                $sacBruto
                *
                0.02,
                2
            );


        $this->agregarDetalle(
            $detalleItems,
            '401',
            $patronalCajaSac,
            1,
            16,
            0,
            '16% Aporte Patronal Caja de Previsión Social sobre aguinaldo'
        );


        $this->agregarDetalle(
            $detalleItems,
            '403',
            $patronalIpsSac,
            1,
            2,
            0,
            '2% Aporte Patronal IPS sobre aguinaldo'
        );


        /*
        |--------------------------------------------------------------------------
        | OTROS DESCUENTOS ASIGNADOS AL EMPLEADO - SAC
        |--------------------------------------------------------------------------
        |
        | Solo se incluyen conceptos particulares que tengan aplica_sac = 1.
        |
        */

        $basesDescuentosSac = [

            'TOTAL_SAC' =>
                $sacBruto,

            /*
            | En una liquidación de SAC, TOTAL_REMUNERATIVO representa la
            | base remunerativa de esta liquidación: el SAC bruto.
            */
            'TOTAL_REMUNERATIVO' =>
                $sacBruto,

            'TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS' =>
                $baseSacMenosObligatorios
        ];


        $this->agregarDescuentosAsignadosEmpleado(
            $detalleItems,
            $conceptosEmpleado,
            $basesDescuentosSac,
            [
                '301',
                '302',
                '303',
                '304',
                '306',
                '307'
            ],
            true
        );


        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */

        $totalRemunerativo =
            $sacBruto;


        $totalNoRemunerativo =
            0;


        $totalAsignaciones =
            0;


        $totalDescuentos = 0;


        foreach (
            $detalleItems
            as
            $item
        ) {

            $codigoDetalle =
                (int)($item['codigo'] ?? 0);


            if (
                $codigoDetalle >= 301
                &&
                $codigoDetalle <= 399
            ) {

                $totalDescuentos +=
                    (float)($item['monto'] ?? 0);
            }
        }


        $neto =
            $totalRemunerativo
            -
            $totalDescuentos;


        return [

            'detalle' =>
                $detalleItems,

            'total_remunerativo' =>
                round(
                    $totalRemunerativo,
                    2
                ),

            'total_descuentos' =>
                round(
                    $totalDescuentos,
                    2
                ),

            'total_no_remunerativo' =>
                0,

            'total_asignaciones' =>
                0,

            'neto' =>
                round(
                    $neto,
                    2
                )
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | AGREGAR ÍTEM AL DETALLE
    |--------------------------------------------------------------------------
    */

    private function agregarDetalle(
        &$items,
        $codigo,
        $monto,
        $cantidad = 1,
        $porcentaje = 0,
        $esManual = 0,
        $observacion = ''
    ) {
        $monto =
            round(
                (float)$monto,
                2
            );


        /*
        |--------------------------------------------------------------------------
        | NO AGREGAR MONTOS CERO O NEGATIVOS
        |--------------------------------------------------------------------------
        */

        if ($monto <= 0) {

            return;
        }


        $items[] = [

            'codigo' =>
                (string)$codigo,

            'monto' =>
                $monto,

            'cantidad' =>
                (float)$cantidad,

            'porcentaje' =>
                (float)$porcentaje,

            'es_manual' =>
                (int)$esManual,

            'observacion' =>
                (string)$observacion
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | AGREGAR DESCUENTOS ASIGNADOS AL EMPLEADO
    |--------------------------------------------------------------------------
    |
    | MANUAL
    |     -> monto_manual.
    |
    | PORCENTAJE
    |     -> porcentaje_manual.
    |     -> base_calculo configurada en Gestión de Conceptos.
    |
    | Bases admitidas en liquidación común:
    |
    | BASICO
    |
    | BASICO_MAS_DEDICACION
    |
    | TOTAL_REMUNERATIVO
    |
    | TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS
    |
    | No se utiliza monto_fijo ni porcentaje general.
    |
    | Compatibilidad histórica:
    |
    | 308 sin base_calculo
    |     -> TOTAL_REMUNERATIVO
    |
    | 310 sin base_calculo
    |     -> TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS
    |
    */

    private function agregarDescuentosAsignadosEmpleado(
        &$detalleItems,
        $conceptosEmpleado,
        $basesCalculo,
        $codigosExcluidos = [],
        $soloAplicaSac = false
    ) {
        foreach (
            $conceptosEmpleado
            as
            $conceptoEmpleado
        ) {

            $codigo =
                (string)(
                    $conceptoEmpleado['codigo']
                    ?? ''
                );


            $codigoNumero =
                (int)$codigo;


            if (
                $codigoNumero < 301
                ||
                $codigoNumero > 399
            ) {

                continue;
            }


            $categoriaConcepto =
                strtoupper(
                    trim(
                        (string)(
                            $conceptoEmpleado['concepto_categoria']
                            ?? ''
                        )
                    )
                );


            if ($categoriaConcepto !== 'DESCUENTO') {

                continue;
            }


            if (
                in_array(
                    $codigo,
                    $codigosExcluidos,
                    true
                )
            ) {

                continue;
            }


            if (
                $soloAplicaSac
                &&
                (int)(
                    $conceptoEmpleado['aplica_sac']
                    ?? 0
                ) !== 1
            ) {

                continue;
            }


            $formaCalculo =
                strtoupper(
                    trim(
                        (string)(
                            $conceptoEmpleado['forma_calculo']
                            ?? ''
                        )
                    )
                );


            if ($formaCalculo === 'FIJO') {

                $formaCalculo = 'MANUAL';
            }


            $montoManual =
                round(
                    (float)(
                        $conceptoEmpleado['monto_manual']
                        ?? 0
                    ),
                    2
                );


            $porcentajeManual =
                (float)(
                    $conceptoEmpleado['porcentaje_manual']
                    ?? 0
                );


            $cantidad =
                (float)(
                    $conceptoEmpleado['cantidad']
                    ?? 1
                );


            if ($cantidad <= 0) {

                $cantidad = 1;
            }


            $observacion =
                !empty(
                    $conceptoEmpleado['observacion']
                )
                    ?
                    $conceptoEmpleado['observacion']
                    :
                    (
                        !empty(
                            $conceptoEmpleado['concepto_nombre']
                        )
                            ?
                            $conceptoEmpleado['concepto_nombre']
                            :
                            'Descuento asignado al empleado'
                    );


            $montoDescuento = 0;
            $porcentajeAplicado = 0;


            if (
                $formaCalculo === 'MANUAL'
                &&
                $montoManual > 0
            ) {

                $montoDescuento =
                    $montoManual;


            } elseif (
                $formaCalculo === 'PORCENTAJE'
                &&
                $porcentajeManual > 0
            ) {

                $baseCalculo =
                    strtoupper(
                        trim(
                            (string)(
                                $conceptoEmpleado['base_calculo']
                                ?? ''
                            )
                        )
                    );


                /*
                |--------------------------------------------------------------------------
                | COMPATIBILIDAD HISTÓRICA 308 / 310
                |--------------------------------------------------------------------------
                |
                | Los conceptos nuevos deben tener base_calculo configurada.
                | Estas dos excepciones solo evitan romper registros históricos.
                |
                */

                if ($baseCalculo === '') {

                    if ($codigo === '308') {

                        $baseCalculo =
                            'TOTAL_REMUNERATIVO';

                    } elseif ($codigo === '310') {

                        $baseCalculo =
                            'TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS';
                    }
                }


                $basesPermitidas = [
                    'BASICO',
                    'BASICO_MAS_DEDICACION',
                    'TOTAL_REMUNERATIVO',
                    'TOTAL_REMUNERATIVO_MENOS_DESCUENTOS_OBLIGATORIOS'
                ];


                if (
                    !in_array(
                        $baseCalculo,
                        $basesPermitidas,
                        true
                    )
                ) {

                    $nombreConcepto =
                        !empty(
                            $conceptoEmpleado['concepto_nombre']
                        )
                            ?
                            $conceptoEmpleado['concepto_nombre']
                            :
                            'Concepto sin nombre';


                    throw new Exception(
                        "El descuento porcentual "
                        . $codigo
                        . " - "
                        . $nombreConcepto
                        . " no tiene una base de cálculo válida configurada."
                    );
                }


                if (
                    !array_key_exists(
                        $baseCalculo,
                        $basesCalculo
                    )
                ) {

                    throw new Exception(
                        "No se pudo determinar la base "
                        . $baseCalculo
                        . " para el descuento "
                        . $codigo
                        . "."
                    );
                }


                $basePorcentaje =
                    (float)$basesCalculo[
                        $baseCalculo
                    ];


                if ($basePorcentaje < 0) {

                    $basePorcentaje = 0;
                }


                $montoDescuento =
                    round(
                        $basePorcentaje
                        *
                        (
                            $porcentajeManual
                            /
                            100
                        ),
                        2
                    );


                $porcentajeAplicado =
                    $porcentajeManual;


            } else {

                continue;
            }


            if ($montoDescuento > 0) {

                $this->agregarDetalle(
                    $detalleItems,
                    $codigo,
                    $montoDescuento,
                    $cantidad,
                    $porcentajeAplicado,
                    1,
                    $observacion
                );
            }
        }
    }



}