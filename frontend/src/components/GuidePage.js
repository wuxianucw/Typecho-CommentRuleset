import React from 'react';
import Box from '@material-ui/core/Box';
import Typography from '@material-ui/core/Typography';
import { makeStyles } from '@material-ui/core/styles';
import Tooltip from '@material-ui/core/Tooltip';
import Link from '@material-ui/core/Link';
import Divider from '@material-ui/core/Divider';
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableContainer from '@material-ui/core/TableContainer';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import Paper from '@material-ui/core/Paper';
import LockOutlinedIcon from '@material-ui/icons/LockOutlined';
import DeleteIcon from '@material-ui/icons/Delete';

const useStyles = makeStyles((theme) => ({
    table: {
        minWidth: 750,
    }
}));

export default function GuidePage() {
    const classes = useStyles();

    return (
        <Typography component="div">
            <Box fontSize="h4.fontSize" m={1}>
                教程：如何优雅地使用评论规则集
                <Box color="text.secondary" fontSize="subtitle1.fontSize" component="span" m={1}>文档版本：v1.0</Box>
            </Box>
            <Box fontSize="body1.fontSize" m={1}>
                Typecho 评论规则集插件（<code>CommentRuleset</code>，后简称“插件”）是一款灵活高效的评论控制插件，其功能的灵活性使得对其的配置较为复杂。为了一定程度上排除这款插件在使用上的疑难，同时对插件用法作出一定的指导，笔者特地撰写此教程，以便插件强大的功能得到充分的使用。
            </Box>
            <Divider />
            <Box fontSize="h5.fontSize" m={1}>
                基础玩法
                <Box color="text.secondary" fontSize="subtitle2.fontSize" component="span" m={1}>快速入门</Box>
            </Box>
            <Box fontSize="body1.fontSize" m={1}>
                <p>如果您对插件启用、禁用、增删规则等操作比较熟悉，可以跳过这部分内容。（话说能看到此文就说明插件已经成功启用了吧……）</p>
                <p>首先让我们了解一下插件的目录结构：</p>
                <pre><code>CommentRuleset<br />├───libs<br />├───runtime<br />└───static</code></pre>
                <p>其中 <code>libs</code> 和 <code>static</code> 目录是固有的，运行时不会被修改；<code>runtime</code> 目录是动态生成的，<strong>必须可写且可执行</strong>，将会存放插件的一些必要数据以及规则的编译结果。</p>
                <p>如果您发现插件目录中还有其他子目录，例如 <code>test</code>、<code>tools</code> 甚至 <code>frontend</code>，除非您非常清楚它们的作用，否则笔者建议删除它们，因为它们不是必要的，且在某些情况下可能带来潜在的风险。</p>
                <p><strong>请注意：在插件处于启用状态时，笔者不建议在任何情况下修改或删除插件目录中的文件。</strong></p>
                <p><code>libs</code> 目录用于存放插件用到的额外 PHP 库。其中 <code>MenuOutputer.php</code> 是笔者在 Typecho 原生后台菜单输出组件的基础上编写的用于输出纯数据形式菜单结构的组件，按照 Typecho 的要求以 <Link href="https://www.gnu.org/licenses/old-licenses/gpl-2.0.html" target="_blank" rel="noopener noreferrer">GNU General Public License v2.0</Link> 进行许可；<code>RuleCompiler.php</code> 是笔者编写的一个将笔者设计的“规则语言”编译到 PHP 的小型编译器（当然，没有什么特别复杂的结构，只是一个简单的工具库），<code>Logger.php</code> 是一个日志记录器，这两者都以 <Link href="https://www.gnu.org/licenses/agpl-3.0.en.html" target="_blank" rel="noopener noreferrer">GNU Affero General Public License v3.0</Link> 进行许可。</p>
                <p><code>static</code> 目录用于存放本地资源文件，包括必要的样式表、Roboto 字体和编译后的 JavaScript 文件，其中主要代码（与插件页面逻辑相关的自有代码）以 <Link href="https://www.gnu.org/licenses/agpl-3.0.en.html" target="_blank" rel="noopener noreferrer">GNU Affero General Public License v3.0</Link> 进行许可，部分依赖库的许可协议见目录下 <code>*.LICENSE.txt</code>。</p>
                <p>下面重点介绍 <code>runtime</code> 目录中的文件组成。</p>
                <p>全新安装的插件在启用前并不存在 <code>runtime</code> 目录，启用插件时，插件将会自动创建这个目录以及目录下的 <code>ruleset.php</code> 规则集索引文件。规则集索引文件用于保存当前情况下的整个规则列表。</p>
                <p>编译规则后，目录下会生成相应的 <code>rule_<Tooltip title="RUID"><span style={{ padding: '2px',backgroundColor: '#e8eaf6' }}>xxxxxx</span></Tooltip>.php</code>。</p>
                <p>这里第一次出现了 RUID 的概念，它的全称是 Rule Unique ID，即规则唯一标识符，是一个字母全部小写的 6 位十六进制数，采用随机的方法生成，用于标识规则。</p>
                <p><strong>再次强调：绝对不要手动修改或删除 <code>runtime</code> 目录下的任何文件！这样做的后果很可能是灾难性的！</strong></p>
                <Divider />
                <p>了解一些基本概念后，我们来尝试新增一条规则。</p>
                <p>在“新增规则”选项卡，我们可以看到“规则名称”、“优先级”、“启用规则”、“规则备注”、“规则内容”以及“编译规则”六个配置项，下表是对它们的详细说明（其中“必要”的“二值”代表这个配置项一定存在且只有两种状态，不存在为空的情况）：</p>
            </Box>
            <TableContainer component={Paper}>
                <Table className={classes.table}>
                    <TableHead>
                        <TableRow>
                            <TableCell>配置项</TableCell>
                            <TableCell>类型</TableCell>
                            <TableCell>映射字段名及类型</TableCell>
                            <TableCell>必要</TableCell>
                            <TableCell>说明</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        <TableRow>
                            <TableCell>规则名称</TableCell>
                            <TableCell><code>string</code></TableCell>
                            <TableCell><code>name: string</code></TableCell>
                            <TableCell>是</TableCell>
                            <TableCell>当前规则的名称。规范清晰的命名将有助于日后对规则的维护。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>优先级</TableCell>
                            <TableCell><code>int</code></TableCell>
                            <TableCell><code>priority: int</code></TableCell>
                            <TableCell>是</TableCell>
                            <TableCell>一个大于或等于 0 且小于或等于 99999 的整数。当规则有冲突时，优先级高的规则将会优先生效。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>启用规则</TableCell>
                            <TableCell><code>bool</code></TableCell>
                            <TableCell><code>status: string[]</code></TableCell>
                            <TableCell>二值</TableCell>
                            <TableCell>用于控制规则的启用状态。如果设置为启用，则必须编译规则。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>规则备注</TableCell>
                            <TableCell><code>string</code></TableCell>
                            <TableCell><code>remark: string</code></TableCell>
                            <TableCell>否</TableCell>
                            <TableCell>为当前规则添加的备注信息，可以是任意内容。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>规则内容</TableCell>
                            <TableCell><code>RuleData | string</code></TableCell>
                            <TableCell><code>editMode: int</code><br /><code>ruleText: string</code></TableCell>
                            <TableCell>是</TableCell>
                            <TableCell>当前规则的内容，决定该规则的行为。<br /><code>type RuleData = [string, string, string, string, string[], string[], string | undefined][];</code></TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>编译规则</TableCell>
                            <TableCell><code>bool</code></TableCell>
                            <TableCell><code>status: string[]</code></TableCell>
                            <TableCell>二值</TableCell>
                            <TableCell>指定是否编译这条规则。如果需要启用规则，则规则必须编译；当编译失败时，规则无法被启用。</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </TableContainer>
            <Box fontSize="body1.fontSize" m={1}>
                <p>这里我们尝试新增一条在检测到评论中包含网址时，将其标为待审核的规则。这是一种很简陋的防御广告的基本方法，但是很具有代表性，可以帮助您了解规则创建的一般流程。</p>
                <p>规则名称可以按照您的喜好填写，例如“检测网址”；规则备注同理，例如“对于包含网址的评论，将其标为待审核以便手动筛选”。目前我们暂时不用考虑优先级问题，保持默认的“10”即可。勾选“编译规则”和“启用规则”（后者勾选时会自动勾选前者）以确保规则生效，接下来我们开始设置规则内容。</p>
                <p>这里仅讲解默认的“所见即所得编辑模式”的使用方法，“规则文本编辑模式”的用法将在后续“进阶指导”以及“高级技巧”部分展开。在这个模式下，一切都是语义化的，规则可以按照字面意思来理解。</p>
                <p>为了书写规则内容，首先需要构思规则的逻辑。要检测评论中的网址，被检测的对象应该是“评论内容”。下表列出了目前所有可供检测的对象：</p>
            </Box>
            <TableContainer component={Paper}>
                <Table className={classes.table}>
                    <TableHead>
                        <TableRow>
                            <TableCell>对象</TableCell>
                            <TableCell>内部名称</TableCell>
                            <TableCell>类型</TableCell>
                            <TableCell>说明</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        <TableRow>
                            <TableCell>UID</TableCell>
                            <TableCell><code>uid</code></TableCell>
                            <TableCell><code>int</code></TableCell>
                            <TableCell>发表评论用户的 UID。游客为 0，博主为 1，其他注册用户视情况而定。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>昵称</TableCell>
                            <TableCell><code>nick</code></TableCell>
                            <TableCell><code>string</code></TableCell>
                            <TableCell>发表评论用户的昵称。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>邮箱</TableCell>
                            <TableCell><code>email</code></TableCell>
                            <TableCell><code>string</code></TableCell>
                            <TableCell>发表评论用户的邮箱。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>个人主页</TableCell>
                            <TableCell><code>url</code></TableCell>
                            <TableCell><code>string?</code></TableCell>
                            <TableCell>发表评论用户预留的个人主页地址，可能为空。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>评论内容</TableCell>
                            <TableCell><code>content</code></TableCell>
                            <TableCell><code>string</code></TableCell>
                            <TableCell>评论的完整内容。如果评论是以 Markdown 格式书写的，此处是解析前的内容。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>评论有效长度</TableCell>
                            <TableCell><code>length</code></TableCell>
                            <TableCell><code>int</code></TableCell>
                            <TableCell>去除所有空白字符后评论的长度。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>IP</TableCell>
                            <TableCell><code>ip</code></TableCell>
                            <TableCell><code>string</code></TableCell>
                            <TableCell>发表评论用户的 IP 地址。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>User-Agent</TableCell>
                            <TableCell><code>ua</code></TableCell>
                            <TableCell><code>string</code></TableCell>
                            <TableCell>发表评论用户的 User-Agent 数据。</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </TableContainer>
            <Box fontSize="body1.fontSize" m={1}>
                <p>考虑最简单的检测方法，可以使用“包含”运算符，检查评论内容是否包含“http”。如果是，标记为待审核；否则无动作。输入这些内容并保存，我们就完成了我们的第一条最简单的规则。</p>
                <p>但是这样做的缺陷十分明显：误判率太高。可能访客输入的是“HTTP 协议”，而“包含”既不区分大小写，也不关注上下文，尽管速度相对较快，但局限性比较大。即使更改为检测“http://”和“https://”，也同样存在很大问题。因此，我们可以考虑用正则表达式优化这条规则。</p>
                <p>要使用正则表达式，首先需要把运算符改为“符合（正则表达式）”，然后书写正则表达式即可。简单的网址匹配可以使用 <code>/https?:\/\/[a-zA-Z0-9]+\.[a-zA-Z0-9]+/</code>，注意这个正则表达式并不是对网址的完整匹配，仅仅是一个特征识别。如果需要一个完整匹配，可以查阅正则表达式的相关资料。</p>
                <p>有时，博主可能会在评论中发送网址，但这同样会被标记为待审核，显得很麻烦。我们可以通过增加判断逻辑来解决这个问题：先检测是否是博主，如果是则直接通过评论，否则再进行后续检测。这个修改比较棘手，因为它涉及判断逻辑的插入，需要重写所有的内容（简便方法将在后续介绍）。首先，将 <code>#Main</code> 中内容改为“如果 UID 等于 1”（一般情况下博主的 UID 为 1，游客为 0），“那么 通过评论 否则 继续判断”，在选择“继续判断”后，会立刻生成一个新的判断块，在该判断块中重写刚刚的“如果 评论内容 符合（正则表达式）……”即可。</p>
                <p>保存规则，现在它已经具备简陋但很有效的功能了。这就是新增/编辑规则的一般步骤。</p>
                <Divider />
                <p>在确保规则无误后，笔者建议锁定规则以降低误操作的风险。在“规则总览”页面点击 <LockOutlinedIcon /> 图标即可。解锁规则同理。</p>
                <p>有时，您可能需要删除规则。同样在“规则总览”页面操作，首先选中欲删除的规则（可以是多个），然后点击表格右上角的 <DeleteIcon /> 图标即可。在执行删除操作时请谨慎，因为该操作不可逆。</p>
                <p>以上就是插件基本用法。在实践中，您可以获得对插件更加全面的理解。如果您对以上内容存在疑问，不妨动手试试看，很多事物是不言自明的。</p>
            </Box>
            <Divider />
            <Box fontSize="h5.fontSize" m={1}>
                进阶指导
                <Box color="text.secondary" fontSize="subtitle2.fontSize" component="span" m={1}>这才是常规操作</Box>
            </Box>
            <Box fontSize="body1.fontSize" m={1}>
                <p>如果您现在已经比较熟悉插件的基本操作了，就让我们开始这部分内容吧。</p>
                <p>在实际应用中，规则的优化是很重要的。因此，很有必要在一开始就规划好逻辑。尽管如此，有时调整规则是不可避免也是必要的。所见即所得编辑模式很直观，但在涉及结构修改时显得不那么方便，而且写起来也许不够快。为了解决这个问题，我们引入了“规则文本编辑模式”，这个模式允许我们以一种相对底层的方式控制规则。</p>
                <p>所见即所得编辑模式到规则文本编辑模式的转换是全等的变换。在设计上，规则内容是直接以规则文本的形式保存的，所以前者转换到后者当然不会出现任何问题；但需要注意的是，后者到前者的转换并未经过非常充分的测试，而且会丢弃部分和逻辑本身无关的数据（如注释），尽管它理论上是正确的，但频繁的切换可能会导致一些未知的问题。</p>
                <p>了解其中利弊之后，让我们尝试迈向规则文本编辑模式。仍然考虑前文提到过的检测网址规则，假设现在出现了一位恶意用户，我们希望禁止他的一切评论，逻辑上需要做出什么变更呢？</p>
                <p>在这个情境中，我们可以直接新增一条优先级更高的黑名单规则来快速达成目的，这也是笔者推荐的方式。但如果一定要修改现有规则，则检测并禁止评论的逻辑应该在检测网址的逻辑之前，类似我们之前增加的检测博主 UID 逻辑。此时需要插入判断块，而所见即所得编辑模式并不支持插入，我们直接切换到规则文本编辑模式。</p>
                <p>这个切换不涉及规则的解析，所以点击“切换到规则文本编辑模式”并在弹出的对话框中确定后立刻就能切换。规则文本编辑器基于 Monaco，并配置了有限的语法提示和高亮，初次加载时可能会“Loading...”一段时间。不出意外的话，现在我们能够看到类似这样的文本：</p>
                <pre><code>[ uid == 1 ] : accept ! [ content ~ /https?:\/\/[a-zA-Z0-9]+\.[a-zA-Z0-9]+/ ] : review ! skip ; ;</code></pre>
                <p>这是 Rule 语法，方括号表示一个判断语句，紧跟在冒号后的是“那么”，感叹号后的是“否则”。它的基本形式是 <code>[ a ?? b ] : then ! else ;</code>，其中 <code>a</code> 就是检测对象的内部名称（见上表），<code>??</code> 代表运算符，目前所有支持的运算符有：{['==', '!=', '<', '>', '<=', '>=', '<-'].map((opt) => (<><code>{opt}</code>&nbsp;</>))}<code>~</code>，其中 <code>{"<-"}</code> 和 <code>~</code> 分别代表“包含”和“符合（正则表达式）”。</p>
                <p><code>b</code> 应该是一个常量值（数字、字符串或正则表达式），<code>then</code> 和 <code>else</code> 分别代表判断语句成立或不成立时执行的操作，可以是一个具体的操作或一个进一步的判断块。所有可用的操作如下：</p>
            </Box>
            <TableContainer component={Paper}>
                <Table className={classes.table}>
                    <TableHead>
                        <TableRow>
                            <TableCell>操作名</TableCell>
                            <TableCell>内部名称</TableCell>
                            <TableCell>说明</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        <TableRow>
                            <TableCell>无动作</TableCell>
                            <TableCell><code>skip</code></TableCell>
                            <TableCell>本规则不给出指示，保持原状或交由下级规则继续判断。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>通过评论</TableCell>
                            <TableCell><code>accept</code></TableCell>
                            <TableCell>直接通过当前评论，设置其状态为正常，评论将可以展示到前台。常用于白名单。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>标记为待审核</TableCell>
                            <TableCell><code>review</code></TableCell>
                            <TableCell>标记当前评论为待审核以便人工复查。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>标记为垃圾</TableCell>
                            <TableCell><code>spam</code></TableCell>
                            <TableCell>标记当前评论为垃圾评论。垃圾评论仅在后台可见。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>拒绝评论</TableCell>
                            <TableCell><code>deny</code></TableCell>
                            <TableCell>拒绝当前评论，当前评论将不会被储存到数据库中，同时前台将显示评论失败。</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </TableContainer>
            <Box fontSize="body1.fontSize" m={1}>
                <p>了解必要知识后，就可以进行插入了。新增的逻辑应该是在判断 UID 之后，即：</p>
                <pre><code>[ uid == 1 ] : accept ! [ content ~ /https?:\/\/[a-zA-Z0-9]+\.[a-zA-Z0-9]+/ ] : review ! skip ; ;<br />{"                       ^"}</code></pre>
                <p>直接插入目标逻辑例如 <code>[ ip == '0.0.0.0' ] : deny ! ... ;</code>，效果如下：</p>
                <pre><code>[ uid == 1 ] : accept ! <Box color="success.main" component="span">[ ip == '0.0.0.0' ] : deny !</Box> [ content ~ /https?:\/\/[a-zA-Z0-9]+\.[a-zA-Z0-9]+/ ] : review ! skip ; <Box color="success.main" component="span">; </Box>;</code></pre>
                <p>最后，切换回所见即所得编辑模式（可能需要一小会时间，因为需要后端解析规则），就能看到新的判断块成功插入了。</p>
            </Box>
            <Divider />
            <Box fontSize="h5.fontSize" m={1}>
                高级技巧
                <Box color="text.secondary" fontSize="subtitle2.fontSize" component="span" m={1}>Let's Speed Up!</Box>
            </Box>
            <Box fontSize="body1.fontSize" m={1}>
                <p>这里介绍 Rule 语法以及编译器相关……</p>
            </Box>
            <Divider />
            <Box fontSize="h5.fontSize" m={1}>
                疑难解答
                <Box color="text.secondary" fontSize="subtitle2.fontSize" component="span" m={1}>好像有哪里不对</Box>
            </Box>
            <Box fontSize="body1.fontSize" m={1}>如果您在使用过程中遇到了问题，这部分内容可能会有所帮助。如果您无法在这里找到答案，可以<Link href="https://github.com/wuxianucw/Typecho-CommentRuleset/issues" target="_blank" rel="noopener noreferrer">在 GitHub 上提出 issue</Link>。</Box>
            <Divider />
            <Box fontSize="h6.fontSize" m={1}>
                常见问题
                <Box color="text.secondary" fontSize="subtitle2.fontSize" component="span" m={1}>Q&amp;A</Box>
            </Box>
            <Box fontSize="body1.fontSize" m={1}></Box>
            <Divider />
            <Box fontSize="h6.fontSize" m={1}>
                编译错误参考
                <Box color="text.secondary" fontSize="subtitle2.fontSize" component="span" m={1}>解析时遇到了雾之湖的妖精</Box>
            </Box>
            <Box fontSize="body1.fontSize" m={1}>注：下表中 <code>xxx</code> 代表任意字符。</Box>
        </Typography>
    );
}
