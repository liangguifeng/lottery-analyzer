<?php

namespace Liangguifeng\LotteryAnalyzer\Enum;

enum LotteryType: string
{
    case QIXING = 'qixing';       // 七星彩
    case FULICAI_3D = 'fucai_3d'; // 福彩3D
    case DALOTTO = 'dalotto';     // 大乐透
}