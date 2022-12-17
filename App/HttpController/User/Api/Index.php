<?php


namespace App\HttpController\User\Api;


use App\HttpController\Auth;
use App\HttpController\Business\User;
use App\Logic\AppService;
use App\Logic\CodeService;
use App\Logic\PayService;
use App\Logic\RewardService;
use App\Logic\TeamService;
use App\Logic\UserService;
use App\Model\Article\Mongo\ArticleModel;
use App\Model\Business\BizAppKVModel;
use App\Model\Business\BizAppModel;
use App\Model\Customer\MemberActiveCodeHistoryModel;
use App\Model\Customer\MemberActiveCodeModel;
use App\Model\Customer\UserClubModel;
use App\Model\Customer\UserModel;
use App\Model\Customer\UserStatModel;
use App\Model\Game\Mongo\DispatchModel;
use App\Model\IM\NoticeTopicModel;
use App\Model\Pay\TokenPriceModel;
use App\Model\Pay\TxnModel;
use App\Model\Pay\UserAssetModel;
use App\Model\Pay\UserContractAssetModel;
use App\Model\Pay\UserDrawAmountModel;
use App\Model\Pay\UserDrwaModel;
use App\Model\Pay\UserMinerAssetModel;
use App\Model\Pay\UserRewardListModel;
use App\Model\Pay\UserRewardModel;
use App\Model\Pay\UserTargetAssetModel;
use App\Model\Platform\EmailModel;
use App\Model\Platform\SmsModel;
use App\Model\Platform\PlatformTokenSettingModel;
use App\Model\Split\Mongo\CheckinModel;
use App\Model\Split\Mongo\InviteCodeModel;
use App\Model\Split\Mongo\RewardPoolModel;
use App\Model\Split\Mongo\SplitModel;
use App\Model\Stat\ProfitByDayModel;
use App\Model\Stat\ProfitTotalModel;
use App\Model\System\RunningLogModel;
use App\Utility\CardUtil;
use App\Utility\MoneyUtil;
use App\Utility\StringUtil;
use App\Utility\Filter;

class Index extends Auth
{

}