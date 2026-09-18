<page backtop="0mm" backbottom="20mm" backleft="0mm" backright="0mm">
    <bookmark title="Personal History (5 years)" level="0" ></bookmark>
    <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">Part 3. Personal References (5 years)</h3>
    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 12px;">
        <tbody>
            <tr>
                <td style="width: 50%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Check status</td>
                <td style="width: 50%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Complete</td>
            </tr>
            <tr>
                <td style="width: 50%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Check completed</td>
                <td style="width: 50%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">
                    {{ \Carbon\Carbon::parse($BPSSApplication->completedDate)->format('d/m/Y') ?? ' '}}
                </td>
            </tr>
        </tbody>
    </table>
    <br><br><br>
    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
        <tbody>
            <tr>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Referee Name</td>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Relationship</td>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Address</td>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Length of Association</td>
            </tr>
            <tr>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $persRef1->referee_name ?? ' ' }}</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $persRef1->relationship ?? ' ' }}</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $persRef1->referee_address_line ?? ' ' }} {{ $persRef1->referee_address_town ?? ' ' }} {{ $persRef1->referee_address_postcode ?? ' ' }}</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-right: 10px;">{{ \Carbon\Carbon::parse($persRef1->date_from)->format('d/m/Y') ?? ' '}} to {{ \Carbon\Carbon::parse($persRef1->date_to)->format('d/m/Y') ?? ' '}}</td>
            </tr>
            <tr>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $persRef2->referee_name ?? ' ' }}</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $persRef2->relationship ?? ' ' }}</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $persRef2->referee_address_line ?? ' ' }} {{ $persRef2->referee_address_town ?? ' ' }} {{ $persRef2->referee_address_postcode ?? ' ' }}</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-right: 10px;">{{ \Carbon\Carbon::parse($persRef2->date_from)->format('d/m/Y') ?? ' '}} to {{ \Carbon\Carbon::parse($persRef2->date_to)->format('d/m/Y') ?? ' '}}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <p style="color: #595959; font-size: 10px">*All of the above references, were contacted to confirm the given dates, relationship and integrity of candidate.</p>
    

    <page_footer>
        <div style="font-size: 11px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; color:rgba(44, 60, 100, 0.4);">
            Page {{$pageNumber}}
        </div>
    </page_footer>
</page>